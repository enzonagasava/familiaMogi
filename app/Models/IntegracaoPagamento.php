<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\BaseModel;

class IntegracaoPagamento extends BaseModel
{
    protected $table = 'integracoes_pagamento';

    protected $fillable = [
        'empresa_id',
        'public_key',
        'access_key'
    ];

}
