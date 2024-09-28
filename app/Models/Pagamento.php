<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Pagamento extends Model
{
    use HasFactory, SoftDeletes;
    protected $table = 'pagamentos';

    protected $fillable = [
        'id_vendendor',  // Verifique se este nome está correto
        'nome_cliente',
        'telefone',
        'tipo_pagamento',
        'valor',
        'desconto',
        'venda',
        'encomenda',
        'observacao_pagamento',
    ];

    protected $casts = [
        'venda' => 'boolean',
        'encomenda' => 'boolean',
    ];

    // Relacionamento muitos-para-muitos com produtos
    public function produtos()
    {
        return $this->belongsToMany(Produto::class, 'pagamento_produto')
                    ->withPivot('quantidade', 'preco_unitario', 'variante_id')
                    ->withTimestamps();
    }

    public function getProdutosComVariantesAttribute()
    {
        return $this->produtos->map(function ($produto) {
            $variantes = Variante::where('id', $produto->pivot->variante_id)->pluck('valor')->implode(', ');
            return "{$produto->nome} - {$variantes}";
        })->implode(' | ');
    }
    public function variantes()
    {
        return $this->belongsToMany(Variante::class, 'produto_variantes', 'produto_id', 'variante_id')
                    ->using(PagamentoProduto::class)
                    ->withTimestamps();
    }
    public function vendedor()
    {
        return $this->belongsTo(User::class, 'id_vendendor');
    }

    /**
     * Validações antes de salvar.
     */
    protected static function booted()
    {
        static::saving(function ($pagamento) {
            if ($pagamento->venda && $pagamento->encomenda) {
                throw new \Exception('Um pagamento não pode ser ao mesmo tempo uma venda e uma encomenda.');
            }
        });
    }
}
