<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tamanho extends Model
{
    protected $fillable = [
        'nome',
    ];
        
    function produtos()
    {
        return $this->belongsToMany(Produto::class, 'produto_tamanho')->withPivot('preco');
    }
}
