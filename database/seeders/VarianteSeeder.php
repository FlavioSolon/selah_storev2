<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use App\Models\Variante;

class VarianteSeeder extends Seeder
{
    public function run()
    {
        // Criar variantes de cor, tamanho e modelo para as camisas
        Variante::insert([
            // Cores
            ['tipo' => 'cor', 'valor' => 'Vermelha'],
            ['tipo' => 'cor', 'valor' => 'Azul'],
            ['tipo' => 'cor', 'valor' => 'Verde'],
            ['tipo' => 'cor', 'valor' => 'Preta'],
            ['tipo' => 'cor', 'valor' => 'Branca'],

            // Tamanhos
            ['tipo' => 'tamanho', 'valor' => 'P'],
            ['tipo' => 'tamanho', 'valor' => 'M'],
            ['tipo' => 'tamanho', 'valor' => 'G'],

            // Modelos
            ['tipo' => 'modelo', 'valor' => 'Com Gola'],
            ['tipo' => 'modelo', 'valor' => 'Sem Gola'],
            ['tipo' => 'modelo', 'valor' => 'Slim'],
        ]);
    }
}
