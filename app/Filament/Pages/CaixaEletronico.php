<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Concerns\InteractsWithForms;
use App\Models\Produto;
use App\Models\Variante;
use Filament\Forms\Components\Section;
use Filament\Notifications\Notification;
use App\Models\Pagamento;
use App\Models\Venda;
use App\Models\Encomenda;

class CaixaEletronico extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-calculator';
    protected static string $view = 'filament.pages.caixa-eletronico';

    public $produto_variante_id;
    public $quantidade = 1;
    public $desconto = 0;
    public $valor = 0;
    public $venda = true;
    public $produtoSelecionado;

    // Carrinho de compras
    public $carrinho = [];
    public $totalCarrinho = 0;

    protected $listeners = ['produtoAdicionado' => 'adicionarAoCarrinho'];

    public $nome_cliente;
    public $telefone;
    public $tipo_pagamento;
    public $observacao_pagamento;

    public function mount()
    {
        $this->valor = 0;
    }

    // Função para calcular o valor do produto
    public function calcularValor()
    {
        if ($this->produtoSelecionado) {
            $produto = $this->produtoSelecionado;
            $quantidade = (int) $this->quantidade;
            $desconto = (float) $this->desconto;

            $this->valor = max(0, ($produto->preco * $quantidade) - $desconto);

            $this->venda = $produto->quantidade >= $this->quantidade;
        } else {
            $this->valor = 0;
            $this->venda = true;
        }
    }

    // Atualiza o produto selecionado e carrega as variantes
    public function updatedProdutoVarianteId($produto_variante_id)
    {
        $this->produtoSelecionado = Produto::with('variantes')->find($produto_variante_id);
        $this->calcularValor();
    }

    // Adiciona o produto ao carrinho
    public function adicionarCarrinho()
    {
        if ($this->produtoSelecionado) {
            $produto = $this->produtoSelecionado;
            $quantidade = (int) $this->quantidade;
            $desconto = (float) $this->desconto;

            // Concatena as variantes do produto
            $variantesConcatenadas = $produto->variantes
                ->map(fn($variante) => "{$variante->valor}")
                ->implode(' - ');

            $itemCarrinho = [
                'id' => $produto->id,
                'nome' => "{$produto->nome} - {$variantesConcatenadas}", // Concatena nome e variantes
                'preco_unitario' => $produto->preco,
                'quantidade' => $quantidade,
                'subtotal' => ($produto->preco * $quantidade) - $desconto,
                'desconto' => $desconto,
                'venda' => $this->venda,
            ];

            // Adiciona ao carrinho
            $this->carrinho[] = $itemCarrinho;
            $this->atualizarTotalCarrinho();

            // Reseta os campos
            $this->reset(['produto_variante_id', 'quantidade', 'desconto', 'valor', 'venda']);
        } else {
            Notification::make()
                ->title('Erro: Selecione um produto.')
                ->danger()
                ->send();
        }
    }

    // Remove o produto do carrinho
    public function removerDoCarrinho($index)
    {
        unset($this->carrinho[$index]);
        $this->carrinho = array_values($this->carrinho); // Reindexa o array
        $this->atualizarTotalCarrinho();
    }

    // Calcula o total do carrinho
    public function atualizarTotalCarrinho()
    {
        $this->totalCarrinho = collect($this->carrinho)->sum('subtotal');

        if ($this->totalCarrinho <= 0) {
            Notification::make()
                ->title('Erro: O total do carrinho não pode ser zero.')
                ->danger()
                ->send();
            return false;
        }

        return true;
    }

    // Formulário do Filament
    protected function getFormSchema(): array
    {
        return [
            Section::make('Adicionar Produto')
                ->columns(2)
                ->schema([
                    // Select concatenado com nome do produto e variantes
                    Select::make('produto_variante_id')
                        ->label('Produto e Variantes')
                        ->options(function () {
                            return Produto::with('variantes')
                                ->get()
                                ->mapWithKeys(function ($produto) {
                                    // Concatena as variantes
                                    $variantesConcatenadas = $produto->variantes
                                        ->map(fn($variante) => "{$variante->valor}")
                                        ->implode(' - ');

                                    return [
                                        $produto->id => "{$produto->nome} - {$variantesConcatenadas}"
                                    ];
                                });
                        })
                        ->searchable()  // Adiciona a opção de pesquisar
                        ->required()
                        ->reactive()
                        ->afterStateUpdated(fn() => $this->updatedProdutoVarianteId($this->produto_variante_id)),

                    TextInput::make('quantidade')
                        ->label('Quantidade')
                        ->numeric()
                        ->default(1)
                        ->required()
                        ->reactive()
                        ->afterStateUpdated(fn() => $this->calcularValor()),

                    TextInput::make('desconto')
                        ->label('Desconto')
                        ->numeric()
                        ->default(0)
                        ->reactive()
                        ->afterStateUpdated(fn() => $this->calcularValor()),

                    TextInput::make('valor')
                        ->label('Valor Total')
                        ->numeric()
                        ->disabled(),
                    Toggle::make('venda')
                        ->label('Venda')
                        ->disabled()
                        ->default(true),
                ]),
        ];
    }

    // Finaliza o pagamento
    public function submit()
{
    // Valida os dados do cliente
    $this->validate([
        'nome_cliente' => 'required|string|max:255',
        'telefone' => 'required|string|max:20',
        'tipo_pagamento' => 'required|string|in:PIX,DINHEIRO,CREDITO,DEBITO',
    ]);

    // Verifica se o carrinho está válido
    if (!$this->atualizarTotalCarrinho()) {
        return;
    }

    $user = auth()->user();
    if (!$user) {
        Notification::make()
            ->title('Erro: Usuário não autenticado.')
            ->danger()
            ->send();
        return;
    }

    // Inicialmente, definimos que todos os itens são venda
    $venda = true;

    // Tenta criar o pagamento
    try {
        $pagamento = Pagamento::create([
            'nome_cliente' => $this->nome_cliente,
            'telefone' => $this->telefone,
            'tipo_pagamento' => $this->tipo_pagamento,
            'valor' => $this->totalCarrinho,
            'observacao_pagamento' => $this->observacao_pagamento,
            'id_vendendor' => $user->id,
            'venda' => $venda,
            'encomenda' => false,  // Será ajustado caso não haja estoque
        ]);
    } catch (\Exception $e) {
        Notification::make()
            ->title('Erro ao processar o pagamento.')
            ->danger()
            ->send();
        return;
    }

    // Processa cada item do carrinho
    foreach ($this->carrinho as $item) {
        $produto = Produto::find($item['id']);

        if (!$produto) {
            Notification::make()
                ->title("Erro: Produto não encontrado")
                ->danger()
                ->send();
            continue;
        }

        // Se o produto não tem estoque suficiente ou está sem estoque, marca como encomenda
        if ($produto->quantidade < $item['quantidade'] || !$produto->em_estoque) {
            $venda = false;  // Definimos que o pagamento será uma encomenda

            // Associar o produto ao pagamento como encomenda
            $pagamento->produtos()->attach($produto->id, [
                'quantidade' => $item['quantidade'],
                'preco_unitario' => $item['preco_unitario'],
            ]);

            Encomenda::create([
                'id_pagamento' => $pagamento->id,
                'aprovada' => true,
                'entregue' => false,
            ]);
        } else {
            // Se há estoque suficiente, registra como venda
            $pagamento->produtos()->attach($produto->id, [
                'quantidade' => $item['quantidade'],
                'preco_unitario' => $item['preco_unitario'],
            ]);

            // Atualiza o estoque
            $produto->quantidade -= $item['quantidade'];
            $produto->save();

            // Cria o registro de venda
            Venda::create([
                'id_pagamento' => $pagamento->id,
                'aprovada' => true,
            ]);
        }
    }

    // Atualiza o tipo de pagamento baseado nos itens do carrinho
    $pagamento->update([
        'venda' => $venda,
        'encomenda' => !$venda,
    ]);

    // Limpa o carrinho e o total
    $this->reset(['carrinho', 'totalCarrinho']);
    Notification::make()
        ->title('Transação Concluída com Sucesso!')
        ->success()
        ->send();
}

}
