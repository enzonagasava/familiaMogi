<?php

namespace App\Services\Integrations;

use App\Jobs\Integrations\SendFamiliaMogiWebhookEventJob;
use App\Models\Integrations\EGroceryWebhookEvent;
use Illuminate\Support\Str;

class FamiliaMogiWebhookPublisher
{
    public function publish(string $eventType, array $entity, array $data, ?string $eventId = null): EGroceryWebhookEvent
    {
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
            SendFamiliaMogiWebhookEventJob::dispatch($event->id);
        }

        return $event;
    }

    private function resolveTargetUrl(): string
    {
        $baseUrl = rtrim((string) config('integrations.familia_mogi.base_url', ''), '/');
        $path = '/'.ltrim((string) config('integrations.familia_mogi.webhook_path', '/api/v1/integrations/e-grocery/webhooks'), '/');

        return $baseUrl.$path;
    }
}
