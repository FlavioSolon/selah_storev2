<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Pagamento extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'id_produto',
        'id_vendendor',
        'nome_cliente',
        'telefone',
        'tipo_pagamento',
        'valor',
        'desconto',
        'quantidade',
        'venda',
        'encomenda',
    ];

    protected $casts = [
        'venda' => 'boolean',
        'encomenda' => 'boolean',
    ];

    /**
     * Relação com Produto.
     */
    public function produto()
    {
        return $this->belongsTo(Produto::class, 'id_produto');
    }

    /**
     * Relação com Usuário (Vendedor).
     */
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
