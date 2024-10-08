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
        Schema::create('pagamentos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_vendendor')->constrained('users')->onDelete('set null');
            $table->string('nome_cliente');
            $table->string('telefone');
            $table->string('tipo_pagamento');
            $table->decimal('valor', 10, 2);
            $table->decimal('desconto', 10, 2)->nullable();
            $table->boolean('venda')->default(false);
            $table->boolean('encomenda')->default(false);
            $table->string('observacao_pagamento')->nullable();
            $table->timestamps();
            $table->softDeletes();

        });
        DB::statement('ALTER TABLE pagamentos ADD CHECK (venda != encomenda)');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pagamentos');
    }
};
