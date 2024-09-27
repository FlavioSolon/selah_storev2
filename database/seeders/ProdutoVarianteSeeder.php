<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProdutoVarianteSeeder extends Seeder
{
    public function run()
    {
        // Relacionando produtos e variantes (camisa vermelha, modelo com gola, tamanho P, etc.)
        DB::table('produto_variantes')->insert([
            // Camisa Vermelha (id 1)
            ['produto_id' => 1, 'variante_id' => 1], // Cor Vermelha
            ['produto_id' => 1, 'variante_id' => 6], // Tamanho P
            ['produto_id' => 1, 'variante_id' => 9], // Modelo Com Gola

            // Camisa Azul (id 2)
            ['produto_id' => 2, 'variante_id' => 2], // Cor Azul
            ['produto_id' => 2, 'variante_id' => 7], // Tamanho M
            ['produto_id' => 2, 'variante_id' => 10], // Modelo Sem Gola

            // Camisa Verde (id 3)
            ['produto_id' => 3, 'variante_id' => 3], // Cor Verde
            ['produto_id' => 3, 'variante_id' => 6], // Tamanho P
            ['produto_id' => 3, 'variante_id' => 11], // Modelo Slim

            // Camisa Preta (id 4)
            ['produto_id' => 4, 'variante_id' => 4], // Cor Preta
            ['produto_id' => 4, 'variante_id' => 8], // Tamanho G
            ['produto_id' => 4, 'variante_id' => 9], // Modelo Com Gola

            // Camisa Branca (id 5)
            ['produto_id' => 5, 'variante_id' => 5], // Cor Branca
            ['produto_id' => 5, 'variante_id' => 7], // Tamanho M
            ['produto_id' => 5, 'variante_id' => 11], // Modelo Slim
        ]);
    }
}
