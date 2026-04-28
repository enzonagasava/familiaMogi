<?php

use App\Jobs\Integrations\SendFamiliaMogiWebhookEventJob;
use App\Models\Integrations\EGroceryWebhookEvent;
use App\Services\Integrations\FamiliaMogiWebhookPublisher;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Queue;

beforeEach(function () {
    config([
        'integrations.familia_mogi.base_url' => 'https://familia-mogi.test',
        'integrations.familia_mogi.webhook_secret' => 'webhook-secret',
        'integrations.familia_mogi.webhook_path' => '/api/v1/integrations/e-grocery/webhooks',
        'integrations.familia_mogi.timeout_seconds' => 5,
    ]);

    Queue::fake();
});

it('sends webhook with required headers and hmac signature', function () {
    Http::fake([
        'https://familia-mogi.test/*' => Http::response(['accepted' => true], 202),
    ]);

    $event = EGroceryWebhookEvent::query()->create([
        'event_id' => 'bcb0f6c7-6de1-4fd8-bcd2-3092b9f03d9c',
        'event_type' => 'product.updated',
        'event_time' => '2026-04-27T12:36:01Z',
        'status' => 'pending',
        'target_url' => 'https://familia-mogi.test/api/v1/integrations/e-grocery/webhooks',
        'payload' => [
            'event_id' => 'bcb0f6c7-6de1-4fd8-bcd2-3092b9f03d9c',
            'event_type' => 'product.updated',
            'occurred_at' => '2026-04-27T12:36:01Z',
            'source' => 'nexaSystem_E-grocery',
            'entity' => ['type' => 'product', 'id' => '1', 'version' => 1],
            'data' => ['sku' => '1', 'stock' => 120],
        ],
        'headers' => [],
    ]);

    (new SendFamiliaMogiWebhookEventJob($event->id))->handle();

    Http::assertSent(function ($request) {
        $body = $request->body();
        $expectedSignature = hash_hmac('sha256', $body, 'webhook-secret');

        return $request->url() === 'https://familia-mogi.test/api/v1/integrations/e-grocery/webhooks'
            && $request->hasHeader('X-Event-Id', 'bcb0f6c7-6de1-4fd8-bcd2-3092b9f03d9c')
            && $request->hasHeader('X-Event-Type', 'product.updated')
            && $request->hasHeader('X-Event-Time', '2026-04-27T12:36:01Z')
            && $request->hasHeader('X-Signature', $expectedSignature);
    });

    $event->refresh();
    expect($event->status)->toBe('sent')
        ->and($event->attempt_count)->toBe(1);
});

it('keeps webhook event idempotent by event_id', function () {
    /** @var FamiliaMogiWebhookPublisher $publisher */
    $publisher = app(FamiliaMogiWebhookPublisher::class);

    $first = $publisher->publish('product.updated', [
        'type' => 'product',
        'id' => '1',
        'version' => 1,
    ], [
        'sku' => '1',
        'stock' => 90,
    ], 'a8b0f6c7-6de1-4fd8-bcd2-3092b9f03d9c');

    $second = $publisher->publish('product.updated', [
        'type' => 'product',
        'id' => '1',
        'version' => 2,
    ], [
        'sku' => '1',
        'stock' => 80,
    ], 'a8b0f6c7-6de1-4fd8-bcd2-3092b9f03d9c');

    expect(EGroceryWebhookEvent::query()->where('event_id', 'a8b0f6c7-6de1-4fd8-bcd2-3092b9f03d9c')->count())
        ->toBe(1);

    expect($first->id)->toBe($second->id);

    $job = new SendFamiliaMogiWebhookEventJob($first->id);
    expect($job->backoff)->toBe([60, 300, 900, 3600]);
});
