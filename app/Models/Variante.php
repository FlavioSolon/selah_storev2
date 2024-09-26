<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Variante extends Model
{
    use HasFactory;

    protected $fillable = ['tipo', 'valor'];

    public function produtos()
    {
        return $this->belongsToMany(Produto::class, 'produto_variantes');
    }
}
