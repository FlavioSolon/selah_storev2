<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Concerns\InteractsWithForms;
use App\Models\Produto;
use App\Models\Pagamento;
use App\Models\Venda;
use App\Models\Encomenda;

use Filament\Notifications\Notification;

class CaixaEletronico extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static string $view = 'filament.pages.caixa-eletronico';

    public $produto_id;
    public $nome_cliente;
    public $telefone;
    public $tipo_pagamento;
    public $quantidade = 1;
    public $desconto = 0;
    public $valor;
    public $venda = true;

    public function mount()
    {
        $this->valor = 0;
    }

    public function calcularValor()
    {
        $produto = Produto::find($this->produto_id);

        if ($produto) {
            $preco = (float) $produto->preco;
            $quantidade = (int) $this->quantidade;
            $desconto = (float) $this->desconto;

            $this->valor = max(0, ($preco * $quantidade) - $desconto);

            if ($produto->quantidade < $this->quantidade) {
                $this->venda = false;
            }
        }
    }

    public function submit()
    {
        $this->validate([
            'produto_id' => 'required|exists:produtos,id',
            'nome_cliente' => 'required|string|max:255',
            'telefone' => 'required|string|max:20',
            'tipo_pagamento' => 'required|string|in:PIX,DINHEIRO,CREDITO,DEBITO',
            'quantidade' => 'required|integer|min:1',
            'desconto' => 'nullable|numeric|min:0',
            'venda' => 'required|boolean',
        ]);

        $produto = Produto::find($this->produto_id);

        // Atualizar o estoque
        if ($produto && $this->venda) {
            $produto->quantidade -= $this->quantidade;
            if ($produto->quantidade <= 0) {
                $produto->quantidade = 0;
                $produto->em_estoque = false;
            }
            $produto->save();
        }

        // Cria um pagamento
        $pagamento = Pagamento::create([
            'id_produto' => $this->produto_id,
            'nome_cliente' => $this->nome_cliente,
            'telefone' => $this->telefone,
            'tipo_pagamento' => $this->tipo_pagamento,
            'valor' => $this->valor,
            'desconto' => $this->desconto,
            'quantidade' => $this->quantidade,
            'venda' => $this->venda,
            'encomenda' => !$this->venda,
            'id_vendendor' => auth()->user()->id,
        ]);

        if ($this->venda) {
            Venda::create([
                'id_produto' => $this->produto_id,
                'id_pagamento' => $pagamento->id,
                'aprovada' => true,
                Notification::make()
                    ->title('Produto Vendido!')
                    ->icon('heroicon-o-shopping-cart')
                    ->success()
                    ->send()
            ]);
        } else {
            Encomenda::create([
                'id_produto' => $this->produto_id,
                'id_pagamento' => $pagamento->id,
                'aprovada' => false,
                'entregue' => false,
                Notification::make()
                    ->title('Produto Encomendado!')
                    ->icon('heroicon-o-truck')
                    ->warning()
                    ->send()
            ]);
        }

        // Redefine o formulário
        $this->reset([
            'produto_id',
            'nome_cliente',
            'telefone',
            'tipo_pagamento',
            'quantidade',
            'desconto',
            'valor',
            'venda'
        ]);
    }

    protected function getFormSchema(): array
    {
        return [
            Select::make('produto_id')
                ->label('Produto')
                ->options(Produto::all()->mapWithKeys(function ($produto) {
                    return [
                        $produto->id => "{$produto->nome} - {$produto->tamanho} - {$produto->modelo} - {$produto->cor}"
                    ];
                }))
                ->required()
                ->reactive()
                ->afterStateUpdated(function ($state) {
                    $this->calcularValor();
                }),
            TextInput::make('nome_cliente')
                ->label('Nome do Cliente')
                ->required(),

            TextInput::make('telefone')
                ->label('Telefone')
                ->required(),

            Select::make('tipo_pagamento')
                ->label('Tipo de Pagamento')
                ->options([
                    'PIX' => 'PIX',
                    'DINHEIRO' => 'Dinheiro',
                    'CREDITO' => 'Crédito',
                    'DEBITO' => 'Débito',
                ])
                ->required(),

            TextInput::make('quantidade')
                ->label('Quantidade')
                ->numeric()
                ->default(1)
                ->required()
                ->reactive()
                ->afterStateUpdated(function ($state) {
                    $this->calcularValor();
                }),

            TextInput::make('desconto')
                ->label('Desconto')
                ->numeric()
                ->default(0)
                ->reactive()
                ->afterStateUpdated(function ($state) {
                    $this->calcularValor();
                }),

            TextInput::make('valor')
                ->label('Valor Total')
                ->numeric()
                ->disabled(),

            Toggle::make('venda')
                ->label('Venda')
                ->default(true)
                ->required(),
        ];
    }
}

