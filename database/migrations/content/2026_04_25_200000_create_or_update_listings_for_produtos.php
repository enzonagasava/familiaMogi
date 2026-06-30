<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('listings')) {
            Schema::create('listings', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('imovel_id')->nullable();
                $table->unsignedBigInteger('produto_id')->nullable();
                $table->boolean('anuncio_ativo')->default(true);
                $table->string('anuncio_status')->nullable();
                $table->json('anuncio_tipos')->nullable();
                $table->timestamps();

                $table->index('imovel_id');
                $table->index('produto_id');
                $table->index('anuncio_ativo');
            });

            return;
        }

        Schema::table('listings', function (Blueprint $table) {
            if (!Schema::hasColumn('listings', 'produto_id')) {
                $table->unsignedBigInteger('produto_id')->nullable()->after('imovel_id');
                $table->index('produto_id');
            }

            if (!Schema::hasColumn('listings', 'anuncio_tipos')) {
                $table->json('anuncio_tipos')->nullable()->after('anuncio_status');
            }
        });
    }

    public function down(): void
    {
        if (!Schema::hasTable('listings')) {
            return;
        }

        if (Schema::hasColumn('listings', 'produto_id') || Schema::hasColumn('listings', 'anuncio_tipos')) {
            Schema::table('listings', function (Blueprint $table) {
                if (Schema::hasColumn('listings', 'produto_id')) {
                    $table->dropColumn('produto_id');
                }

                if (Schema::hasColumn('listings', 'anuncio_tipos')) {
                    $table->dropColumn('anuncio_tipos');
                }
            });
        }
    }
};
