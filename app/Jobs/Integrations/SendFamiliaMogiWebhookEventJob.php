<?php

namespace App\Jobs\Integrations;

use App\Models\Integrations\EGroceryWebhookEvent;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class SendFamiliaMogiWebhookEventJob implements ShouldQueue
{
    use Queueable;

    public int $timeout = 120;

    public int $tries = 3;

    public function __construct(public int $webhookEventId)
    {
    }

    public function handle(): void
    {
        Log::info('[SendFamiliaMogiWebhookEventJob] Iniciando job', [
            'webhook_event_id' => $this->webhookEventId,
        ]);


        Log::info('[SendFamiliaMogiWebhookEventJob] Conexão tenant_content configurada', [
        ]);

        $event = EGroceryWebhookEvent::query()->find($this->webhookEventId);

        if (!$event) {
            Log::warning('[SendFamiliaMogiWebhookEventJob] Evento não encontrado', [
                'webhook_event_id' => $this->webhookEventId,
            ]);
            return;
        }

        if ($event->status === 'sent') {
            Log::info('[SendFamiliaMogiWebhookEventJob] Evento já foi enviado, ignorando', [
                'event_id' => $event->event_id,
                'status' => $event->status,
            ]);
            return;
        }

        Log::info('[SendFamiliaMogiWebhookEventJob] Evento encontrado', [
            'event_id' => $event->event_id,
            'event_type' => $event->event_type,
            'status' => $event->status,
            'target_url' => $event->target_url,
        ]);

        // Carrega configurações da integração
        $baseUrl = (string) config('integrations.familia_mogi.base_url', '');
        $secret = (string) config('integrations.familia_mogi.webhook_secret', '');
        $timeout = max(1, (int) config('integrations.familia_mogi.timeout_seconds', 5));
        $token = (string) config('integrations.familia_mogi.api_token', '');

        if ($baseUrl === '' || $secret === '') {
            Log::error('[SendFamiliaMogiWebhookEventJob] Configurações incompletas', [
                'base_url' => $baseUrl ? 'setado' : 'vazio',
                'secret' => $secret ? 'setado' : 'vazio',
            ]);
            throw new RuntimeException('FAMILIA_MOGI_BASE_URL or FAMILIA_MOGI_WEBHOOK_SECRET is not configured.');
        }

        $payload = is_array($event->payload) ? $event->payload : [];
        $rawBody = json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

        if (!is_string($rawBody)) {
            Log::error('[SendFamiliaMogiWebhookEventJob] Falha ao codificar payload', [
                'event_id' => $event->event_id,
            ]);
            throw new RuntimeException('Could not encode webhook payload.');
        }

        $signature = hash_hmac('sha256', $rawBody, $secret);

        $headers = [
            'Content-Type' => 'application/json',
            'X-Event-Id' => (string) ($payload['event_id'] ?? $event->event_id),
            'X-Event-Type' => (string) ($payload['event_type'] ?? $event->event_type),
            'X-Event-Time' => (string) ($payload['occurred_at'] ?? optional($event->event_time)->toIso8601String()),
            'X-Signature' => $signature,
        ];

        if ($token !== '') {
            $headers['Authorization'] = 'Bearer '.$token;
        }

        // Marca como processing
        $event->forceFill([
            'status' => 'processing',
            'attempt_count' => (int) $event->attempt_count + 1,
            'last_attempt_at' => now(),
            'error_message' => null,
        ])->save();

        Log::info('[SendFamiliaMogiWebhookEventJob] Status atualizado para processing', [
            'event_id' => $event->event_id,
            'attempt' => $event->attempt_count,
        ]);

        try {
            $response = Http::timeout($timeout)
                ->withHeaders($headers)
                ->withBody($rawBody, 'application/json')
                ->post($event->target_url);

            Log::info('[SendFamiliaMogiWebhookEventJob] Resposta recebida', [
                'event_id' => $event->event_id,
                'status_code' => $response->status(),
                'success' => $response->successful(),
            ]);

            $event->forceFill([
                'response_status' => $response->status(),
                'response_body' => mb_substr((string) $response->body(), 0, 5000),
                'headers' => $headers,
            ])->save();

            if (!$response->successful()) {
                $errorMsg = sprintf(
                    'familiaMogi webhook rejected event %s with status %d',
                    $event->event_id,
                    $response->status()
                );
                Log::error('[SendFamiliaMogiWebhookEventJob] Webhook rejeitado', [
                    'event_id' => $event->event_id,
                    'status_code' => $response->status(),
                    'response_body' => mb_substr($response->body(), 0, 500),
                ]);
                throw new RuntimeException($errorMsg);
            }

            $event->forceFill([
                'status' => 'sent',
                'delivered_at' => now(),
                'next_retry_at' => null,
                'error_message' => null,
            ])->save();

            Log::info('[SendFamiliaMogiWebhookEventJob] Webhook enviado com sucesso', [
                'event_id' => $event->event_id,
                'event_type' => $event->event_type,
            ]);

        } catch (\Throwable $e) {
            Log::error('[SendFamiliaMogiWebhookEventJob] Exceção durante envio', [
                'event_id' => $event->event_id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            // Relança para que o Laravel marque como falho e chame failed()
            throw $e;
        }
    }

    public function failed(\Throwable $exception): void
    {
        Log::error('[SendFamiliaMogiWebhookEventJob] Job falhou permanentemente', [
            'webhook_event_id' => $this->webhookEventId,
            'error' => $exception->getMessage(),
        ]);

        $event = EGroceryWebhookEvent::query()->find($this->webhookEventId);

        if (!$event) {
            Log::warning('[SendFamiliaMogiWebhookEventJob] Evento não encontrado no failed()', [
                'webhook_event_id' => $this->webhookEventId,
            ]);
            return;
        }

        $event->forceFill([
            'status' => 'failed',
            'next_retry_at' => null,
            'error_message' => mb_substr($exception->getMessage(), 0, 1000),
        ])->save();

        Log::info('[SendFamiliaMogiWebhookEventJob] Status atualizado para failed', [
            'event_id' => $event->event_id,
            'error_message' => $event->error_message,
        ]);
    }
}