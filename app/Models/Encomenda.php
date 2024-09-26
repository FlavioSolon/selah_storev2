<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Encomenda extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'id_pagamento',
        'entregue',
        'aprovada',
    ];

    protected $casts = [
        'entregue' => 'boolean',
        'aprovada' => 'boolean',
    ];

    /**
     * Relação com Pagamento.
     */
    public function pagamento()
    {
        return $this->belongsTo(Pagamento::class, 'id_pagamento');
    }
}
