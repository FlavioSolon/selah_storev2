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
        Schema::create('variantes', function (Blueprint $table) {
            $table->id();
            $table->string('tipo'); // Exemplo: cor, tamanho, modelo
            $table->string('valor'); // Exemplo: vermelho, G, slim
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('variantes');
    }
};
