<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProdutoImagem extends Model
{
    protected $table = 'produto_imagens';
    protected $fillable = [
        'produto_id',
        'user_id',
        'imagem_path',
        'ordem',
    ];

    /**
     * Relacionamento inverso com o anúncio
     */
    public function anuncio(): BelongsTo
    {
        return $this->belongsTo(Produto::class);
    }

    /**
     * Relacionamento com o usuário dono da imagem
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
