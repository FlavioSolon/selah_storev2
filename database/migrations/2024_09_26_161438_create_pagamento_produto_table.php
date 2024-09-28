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
        Schema::create('pagamento_produto', function (Blueprint $table) {
            $table->id();

            $table->foreignId('pagamento_id')
                ->constrained('pagamentos')
                ->onDelete('cascade'); // Deleta os produtos quando o pagamento é excluído

            $table->foreignId('produto_id')
                ->constrained('produtos')
                ->onDelete('restrict'); // Impede deletar produtos que já foram vendidos

            // Novo campo para variantes
            $table->foreignId('variante_id')
                ->nullable()
                ->constrained('variantes')
                ->onDelete('set null'); 

            $table->integer('quantidade');
            $table->decimal('preco_unitario', 8, 2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pagamento_produto');
    }
};
