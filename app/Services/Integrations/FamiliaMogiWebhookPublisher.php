<?php

namespace App\Services\Integrations;

use App\Jobs\Integrations\SendFamiliaMogiWebhookEventJob;
use App\Models\Integrations\EGroceryWebhookEvent;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Throwable;

class FamiliaMogiWebhookPublisher
{
    public function publish(string $eventType, array $entity, array $data, ?string $eventId = null): EGroceryWebhookEvent
    {
        try {
            $tenantContentDatabase = DB::getDatabaseName();
            
            Log::info('[FamiliaMogiWebhookPublisher] Iniciando publicação', [
                'event_type' => $eventType,
                'tenant_database' => $tenantContentDatabase,
                'event_id' => $eventId ?? 'auto',
            ]);

            $eventId = $eventId ?: (string) Str::uuid();
            $eventTime = now()->toIso8601String();

            $payload = [
                'event_id' => $eventId,
                'event_type' => $eventType,
                'occurred_at' => $eventTime,
                'source' => 'nexaSystem_E-grocery',
                'entity' => $entity,
                'data' => $data,
            ];

            $targetUrl = $this->resolveTargetUrl();

            Log::info('[FamiliaMogiWebhookPublisher] Tentando criar evento', [
                'event_id' => $eventId,
                'target_url' => $targetUrl,
            ]);

            $event = EGroceryWebhookEvent::query()->firstOrCreate(
                ['event_id' => $eventId],
                [
                    'event_type' => $eventType,
                    'event_time' => $eventTime,
                    'status' => 'pending',
                    'target_url' => $targetUrl,
                    'payload' => $payload,
                    'headers' => [
                        'X-Event-Id' => $eventId,
                        'X-Event-Type' => $eventType,
                        'X-Event-Time' => $eventTime,
                    ],
                ]
            );

            if ($event->wasRecentlyCreated) {
                Log::info('[FamiliaMogiWebhookPublisher] Evento criado, despachando job', [
                    'event_id' => $eventId,
                    'webhook_event_id' => $event->id,
                ]);

                SendFamiliaMogiWebhookEventJob::dispatch($event->id);
            } else {
                Log::info('[FamiliaMogiWebhookPublisher] Evento já existia, ignorando duplicata', [
                    'event_id' => $eventId,
                    'existing_status' => $event->status,
                ]);
            }

            return $event;

        } catch (Throwable $e) {
            Log::error('[FamiliaMogiWebhookPublisher] Falha ao publicar evento', [
                'event_type' => $eventType,
                'event_id' => $eventId ?? 'unknown',
                'entity' => $entity,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            // Relança para que o chamador saiba que falhou
            throw $e;
        }
    }

    private function resolveTargetUrl(): string
    {
        try {
            $baseUrl = rtrim((string) config('integrations.familia_mogi.base_url', ''), '/');
            $path = '/'.ltrim((string) config('integrations.familia_mogi.webhook_path', '/api/v1/integrations/e-grocery/webhooks'), '/');

            $fullUrl = $baseUrl . $path;

            Log::info('[FamiliaMogiWebhookPublisher] URL resolvida', [
                'base_url' => $baseUrl,
                'path' => $path,
                'full_url' => $fullUrl,
            ]);

            return $fullUrl;

        } catch (Throwable $e) {
            Log::error('[FamiliaMogiWebhookPublisher] Falha ao resolver URL', [
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }
}