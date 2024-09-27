<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use App\Models\Produto;

class ProdutoSeeder extends Seeder
{
    public function run()
    {
        // Cria 5 produtos da categoria 'Camisa' (id 1)
        Produto::insert([
            ['nome' => 'Camisa Vermelha', 'preco' => 100.00, 'quantidade' => 50, 'em_estoque' => true, 'categoria_id' => 1],
            ['nome' => 'Camisa Azul', 'preco' => 120.00, 'quantidade' => 40, 'em_estoque' => true, 'categoria_id' => 1],
            ['nome' => 'Camisa Verde', 'preco' => 90.00, 'quantidade' => 60, 'em_estoque' => true, 'categoria_id' => 1],
            ['nome' => 'Camisa Preta', 'preco' => 150.00, 'quantidade' => 30, 'em_estoque' => true, 'categoria_id' => 1],
            ['nome' => 'Camisa Branca', 'preco' => 110.00, 'quantidade' => 55, 'em_estoque' => true, 'categoria_id' => 1],
        ]);
    }
}
