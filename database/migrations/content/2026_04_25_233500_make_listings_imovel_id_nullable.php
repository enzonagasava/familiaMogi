<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('listings') || !Schema::hasColumn('listings', 'imovel_id')) {
            return;
        }

        // Necessário para tenants legados onde imovel_id ainda está NOT NULL.
        DB::statement('ALTER TABLE listings ALTER COLUMN imovel_id DROP NOT NULL');
    }

    public function down(): void
    {
        // Não reverte para NOT NULL para evitar quebrar anúncios de produto já existentes.
    }
};
