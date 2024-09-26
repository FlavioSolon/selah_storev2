<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Pagamento extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'id_vendendor',
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
                    ->withPivot('quantidade', 'preco_unitario');
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
