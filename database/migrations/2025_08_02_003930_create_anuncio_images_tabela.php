<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('anuncio_imagens', function (Blueprint $table) {
            $table->id();
            $table->foreignId('anuncio_id')->constrained('anuncios')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('imagem_path'); // caminho da imagem
            $table->integer('ordem')->default(0); // ordem da imagem no anúncio
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('anuncio_imagens');
    }
};
