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
        'em_estoque',
        'categoria_id',
    ];

    // Relacionamento com a tabela categorias
    public function categoria()
    {
        return $this->belongsTo(Categoria::class);
    }

    // Relacionamento muitos-para-muitos com variantes
    public function variantes()
    {
        return $this->belongsToMany(Variante::class, 'produto_variantes');
    }

    public function pagamentos()
    {
        return $this->hasMany(Pagamento::class);
    }

    public function vendas()
    {
        return $this->hasMany(Venda::class);
    }

    public function encomendas()
    {
        return $this->hasMany(Encomenda::class);
    }
}

