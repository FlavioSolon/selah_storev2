<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Venda extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'id_produto',
        'id_pagamento',
        'aprovada',
    ];

    protected $casts = [
        'aprovada' => 'boolean',
    ];

    /**
     * Relação com Produto.
     */
    public function produto()
    {
        return $this->belongsTo(Produto::class, 'id_produto');
    }

    /**
     * Relação com Pagamento.
     */
    public function pagamento()
    {
        return $this->belongsTo(Pagamento::class, 'id_pagamento');
    }
}
