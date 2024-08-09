<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Produto extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'nome',
        'preco',
        'quantidade',
        'tipo',
        'modelo',
        'cor',
        'tamanho',
        'em_estoque',
    ];

    /**
     * Relacionamento com Pagamento.
     */
    public function pagamentos()
    {
        return $this->hasMany(Pagamento::class, 'id_produto');
    }

    /**
     * Relacionamento com Encomenda.
     */
    public function encomendas()
    {
        return $this->hasMany(Encomenda::class, 'id_produto');
    }

    /**
     * Relacionamento com Venda.
     */
    public function vendas()
    {
        return $this->hasMany(Venda::class, 'id_produto');
    }
}
