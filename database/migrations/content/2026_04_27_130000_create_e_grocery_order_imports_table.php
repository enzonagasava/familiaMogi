<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('e_grocery_order_imports', function (Blueprint $table) {
            $table->id();
            $table->string('external_order_id')->unique();
            $table->string('source', 64)->default('familiaMogi-api')->index();
            $table->string('status', 32)->default('received')->index();
            $table->foreignId('gerenciar_pedido_id')->nullable()->constrained('gerenciar_pedidos')->nullOnDelete();
            $table->string('panel_order_id', 120)->nullable()->index();
            $table->json('request_payload');
            $table->json('normalized_payload')->nullable();
            $table->json('response_payload')->nullable();
            $table->timestamp('processed_at')->nullable()->index();
            $table->text('error_message')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('e_grocery_order_imports');
    }
};
