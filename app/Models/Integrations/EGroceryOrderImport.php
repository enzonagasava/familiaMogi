<?php

namespace App\Models\Integrations;

use App\Models\BaseModel;

class EGroceryOrderImport extends BaseModel
{
    protected $connection = 'tenant_content';

    protected $table = 'e_grocery_order_imports';

    protected $fillable = [
        'external_order_id',
        'source',
        'status',
        'gerenciar_pedido_id',
        'panel_order_id',
        'request_payload',
        'normalized_payload',
        'response_payload',
        'processed_at',
        'error_message',
    ];

    protected $casts = [
        'request_payload' => 'array',
        'normalized_payload' => 'array',
        'response_payload' => 'array',
        'processed_at' => 'datetime',
    ];
}
