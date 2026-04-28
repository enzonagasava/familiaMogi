<?php

namespace App\Models\Integrations;

use App\Models\BaseModel;

class EGroceryWebhookEvent extends BaseModel
{
    protected $connection = 'tenant_content';

    protected $table = 'e_grocery_webhook_events';

    protected $fillable = [
        'event_id',
        'event_type',
        'event_time',
        'status',
        'target_url',
        'payload',
        'headers',
        'attempt_count',
        'last_attempt_at',
        'next_retry_at',
        'delivered_at',
        'response_status',
        'response_body',
        'error_message',
    ];

    protected $casts = [
        'event_time' => 'datetime',
        'payload' => 'array',
        'headers' => 'array',
        'last_attempt_at' => 'datetime',
        'next_retry_at' => 'datetime',
        'delivered_at' => 'datetime',
    ];
}
