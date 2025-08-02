<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Anuncio extends Model
{
        protected $fillable = [
        'user_id',
        'nome',
        'descricao',
        'estoque',
        'tamanhos',
    ];

    protected $casts = [
        'tamanhos' => 'array', // cast para json array
    ];

    /**
     * Relacionamento 1:N com imagens do anúncio
     */
    public function imagens(): HasMany
    {
        return $this->hasMany(AnuncioImagem::class)->orderBy('ordem');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
