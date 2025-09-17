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
        Schema::create('anuncios', function (Blueprint $table) {
            $table->id();
            $table->string('nome');
            $table->text('descricao')->nullable();
            $table->integer('estoque')->default(0);
            $table->json('tamanhos')->nullable(); // array json dos tamanhos
            $table->timestamps();
        });
    }
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('produto_tamanho', function (Blueprint $table) {
            $table->dropForeign(['produto_id']);
        });

        Schema::dropIfExists('anuncios');
    }
};
