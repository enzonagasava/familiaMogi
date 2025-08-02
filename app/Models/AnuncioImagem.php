<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AnuncioImagem extends Model
{
    protected $table = 'anuncio_imagens';
    protected $fillable = [
        'anuncio_id',
        'user_id',
        'imagem_path',
        'ordem',
    ];

    /**
     * Relacionamento inverso com o anúncio
     */
    public function anuncio(): BelongsTo
    {
        return $this->belongsTo(Anuncio::class);
    }

    /**
     * Relacionamento com o usuário dono da imagem
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
