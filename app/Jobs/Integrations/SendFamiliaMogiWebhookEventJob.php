<?php

namespace App\Jobs\Integrations;

use App\Models\Integrations\EGroceryWebhookEvent;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use RuntimeException;

class SendFamiliaMogiWebhookEventJob implements ShouldQueue
{
    use Queueable;

    public int $tries = 5;

    public array $backoff = [60, 300, 900, 3600];

    public function __construct(public int $webhookEventId)
    {
    }

    public function handle(): void
    {
        $event = EGroceryWebhookEvent::query()->find($this->webhookEventId);

        if (!$event || $event->status === 'sent') {
            return;
        }

        $baseUrl = (string) config('integrations.familia_mogi.base_url', '');
        $secret = (string) config('integrations.familia_mogi.webhook_secret', '');
        $timeout = max(1, (int) config('integrations.familia_mogi.timeout_seconds', 5));
        $token = (string) config('integrations.familia_mogi.api_token', '');

        if ($baseUrl === '' || $secret === '') {
            throw new RuntimeException('FAMILIA_MOGI_BASE_URL or FAMILIA_MOGI_WEBHOOK_SECRET is not configured.');
        }

        $payload = is_array($event->payload) ? $event->payload : [];
        $rawBody = json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

        if (!is_string($rawBody)) {
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

        $event->forceFill([
            'status' => 'processing',
            'attempt_count' => (int) $event->attempt_count + 1,
            'last_attempt_at' => now(),
            'error_message' => null,
        ])->save();

        $response = Http::timeout($timeout)
            ->withHeaders($headers)
            ->withBody($rawBody, 'application/json')
            ->post($event->target_url);

        $event->forceFill([
            'response_status' => $response->status(),
            'response_body' => mb_substr((string) $response->body(), 0, 5000),
            'headers' => $headers,
        ])->save();

        if (!$response->successful()) {
            throw new RuntimeException(sprintf(
                'familiaMogi webhook rejected event %s with status %d',
                $event->event_id,
                $response->status()
            ));
        }

        $event->forceFill([
            'status' => 'sent',
            'delivered_at' => now(),
            'next_retry_at' => null,
            'error_message' => null,
        ])->save();

        Log::info('familiaMogi webhook sent', [
            'event_id' => $event->event_id,
            'event_type' => $event->event_type,
        ]);
    }

    public function failed(\Throwable $exception): void
    {
        $event = EGroceryWebhookEvent::query()->find($this->webhookEventId);

        if (!$event) {
            return;
        }

        $event->forceFill([
            'status' => 'failed',
            'next_retry_at' => null,
            'error_message' => mb_substr($exception->getMessage(), 0, 1000),
        ])->save();

        Log::error('familiaMogi webhook failed', [
            'event_id' => $event->event_id,
            'event_type' => $event->event_type,
            'error' => $exception->getMessage(),
        ]);
    }
}
