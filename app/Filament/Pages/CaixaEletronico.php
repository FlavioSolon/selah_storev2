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
use Filament\Forms\Components\Section;

use Filament\Notifications\Notification;

class CaixaEletronico extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-calculator';

    protected static string $view = 'filament.pages.caixa-eletronico';

    public $produto_id;
    public $nome_cliente;
    public $telefone;
    public $tipo_pagamento;
    public $quantidade = 1;
    public $desconto = 0;
    public $valor;
    public $venda = true;

    public $observacao_pagamento;

    public function mount()
    {
        $this->valor = 0;
    }

    public function calcularValor()
    {
        if (!empty($this->produto_id)) {
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
        } else {
            $this->valor = 0;
            $this->venda = true;
        }
    }


    public function submit()
{
    $this->validate([
        'produto_id' => 'nullable|exists:produtos,id',
        'nome_cliente' => 'required|string|max:255',
        'telefone' => 'required|string|max:20',
        'tipo_pagamento' => 'required|string|in:PIX,DINHEIRO,CREDITO,DEBITO',
        'quantidade' => 'required|integer|min:1',
        'desconto' => 'nullable|numeric|min:0',
        'venda' => 'required|boolean',
    ]);

    $produto = Produto::find($this->produto_id);

    if ($produto) {
        // Se o produto está em estoque e a venda é verdadeira, atualize o estoque
        if ($produto->em_estoque && $this->venda) {
            $produto->quantidade -= $this->quantidade;
            if ($produto->quantidade <= 0) {
                $produto->quantidade = 0;
                $produto->em_estoque = false;
            }
            $produto->save();
        } else {
            // Se o produto não está em estoque, não é possível vender, então considere como encomenda
            $this->venda = false;
        }
    }

    // Cria um pagamento
    $pagamento = Pagamento::create([
        'id_produto' => $this->produto_id,
        'nome_cliente' => $this->nome_cliente,
        'observacao_pagamento' => $this->observacao_pagamento,
        'telefone' => $this->telefone,
        'tipo_pagamento' => $this->tipo_pagamento,
        'valor' => $this->valor,
        'desconto' => $this->desconto,
        'quantidade' => $this->quantidade,
        'venda' => $this->venda,
        'encomenda' => !$this->venda,
        'id_vendendor' => auth()->user()->id,
    ]);

    // Se o produto está em estoque e é uma venda, cria uma venda
    if ($produto && $produto->em_estoque && $this->venda) {
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
    } else if (!$this->venda) {
        // Se não é uma venda, cria uma encomenda
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

    // Resetar os campos do formulário
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
        Section::make('Informações Básicas')
            ->columns([
                'sm' => 2, // 2 colunas em dispositivos pequenos
                'md' => 2, // 2 colunas em dispositivos médios
                'lg' => 2, // 2 colunas em dispositivos grandes
                'xl' => 4, // 4 colunas em dispositivos extra grandes
                '2xl' => 4, // 4 colunas em dispositivos extra extra grandes
            ])
            ->schema([
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
                    })
                    ->columnSpan([
                        'sm' => 2,
                        'md' => 2,
                        'lg' => 2,
                        'xl' => 2,
                        '2xl' => 2,
                    ]),

                Select::make('tipo_pagamento')
                    ->label('Tipo de Pagamento')
                    ->options([
                        'PIX' => 'PIX',
                        'DINHEIRO' => 'Dinheiro',
                        'CREDITO' => 'Crédito',
                        'DEBITO' => 'Débito',
                    ])
                    ->required()
                    ->columnSpan([
                        'sm' => 2,
                        'md' => 2,
                        'lg' => 2,
                        'xl' => 2,
                        '2xl' => 2,
                    ]),

                TextInput::make('nome_cliente')
                    ->label('Nome do Cliente')
                    ->required()
                    ->columnSpan([
                        'sm' => 1,
                        'md' => 2,
                        'lg' => 2,
                        'xl' => 2,
                        '2xl' => 2,
                    ]),

                TextInput::make('telefone')
                    ->label('Telefone')
                    ->required()
                    ->columnSpan([
                        'sm' => 1,
                        'md' => 2,
                        'lg' => 2,
                        'xl' => 2,
                        '2xl' => 2,
                    ]),
            ]),

        Section::make('Detalhes da Venda')
            ->columns([
                'sm' => 1, // 1 coluna em dispositivos pequenos
                'md' => 2, // 2 colunas em dispositivos médios
                'lg' => 3, // 3 colunas em dispositivos grandes
                'xl' => 4, // 4 colunas em dispositivos extra grandes
                '2xl' => 4, // 4 colunas em dispositivos extra extra grandes
            ])
            ->schema([
                TextInput::make('quantidade')
                    ->label('Quantidade')
                    ->numeric()
                    ->default(1)
                    ->required()
                    ->reactive()
                    ->afterStateUpdated(function ($state) {
                        $this->calcularValor();
                    })
                    ->columnSpan([
                        'sm' => 1,
                        'md' => 1,
                        'lg' => 1,
                        'xl' => 1,
                        '2xl' => 1,
                    ]),

                TextInput::make('desconto')
                    ->label('Desconto')
                    ->numeric()
                    ->default(0)
                    ->reactive()
                    ->afterStateUpdated(function ($state) {
                        $this->calcularValor();
                    })
                    ->columnSpan([
                        'sm' => 1,
                        'md' => 1,
                        'lg' => 1,
                        'xl' => 1,
                        '2xl' => 1,
                    ]),

                TextInput::make('valor')
                    ->label('Valor Total')
                    ->numeric()
                    ->disabled()
                    ->columnSpan([
                        'sm' => 1,
                        'md' => 1,
                        'lg' => 1,
                        'xl' => 1,
                        '2xl' => 1,
                    ]),

                Toggle::make('venda')
                    ->label('Venda')
                    ->default(true)
                    ->required()
                    ->columnSpan([
                        'sm' => 1,
                        'md' => 1,
                        'lg' => 1,
                        'xl' => 1,
                        '2xl' => 1,
                    ]),
            ]),

        Section::make('Observações')
            ->columns([
                'sm' => 1, // 1 coluna em dispositivos pequenos
                'md' => 1, // 1 coluna em dispositivos médios
                'lg' => 1, // 1 coluna em dispositivos grandes
                'xl' => 1, // 1 coluna em dispositivos extra grandes
                '2xl' => 1, // 1 coluna em dispositivos extra extra grandes
            ])
            ->schema([
                TextInput::make('observacao_pagamento')
                    ->label('Observação do Pagamento')
                    ->columnSpan('full'), // Ocupa toda a largura da linha
            ]),
    ];
}

}

