<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PagamentoProduto extends Model
{
    use HasFactory;

    protected $table = 'pagamento_produto';

    protected $fillable = [
        'pagamento_id',
        'produto_id',
        'quantidade',
        'preco_unitario',
        'variante_id', // Incluindo o variante_id, se aplicável
    ];

    // Relacionamento com o modelo Pagamento
    public function pagamento()
    {
        return $this->belongsTo(Pagamento::class);
    }

    // Relacionamento com o modelo Produto
    public function produto()
    {
        return $this->belongsTo(Produto::class);
    }

    // Relacionamento com o modelo Variante
    public function variante()
    {
        return $this->belongsTo(Variante::class);
    }
}
