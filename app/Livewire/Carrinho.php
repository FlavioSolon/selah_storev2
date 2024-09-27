<?php

namespace App\Livewire;

use App\Models\Pagamento;
use App\Models\Produto;
use App\Models\Venda;
use App\Models\Encomenda;
use Livewire\Component;
use Filament\Notifications\Notification;

class Carrinho extends Component
{
    public $carrinho = [];
    public $totalCarrinho = 0;

    protected $listeners = ['produtoAdicionado' => 'adicionarAoCarrinho'];

    public $nome_cliente;
    public $telefone;
    public $tipo_pagamento;
    public $observacao_pagamento;

    public function adicionarAoCarrinho($item)
    {
        $this->carrinho[] = $item;
        $this->atualizarTotalCarrinho();
    }

    public function removerDoCarrinho($index)
    {
        unset($this->carrinho[$index]);
        $this->carrinho = array_values($this->carrinho); // Reindexa o array
        $this->atualizarTotalCarrinho();
    }

    public function atualizarTotalCarrinho()
    {
        $this->totalCarrinho = collect($this->carrinho)->sum('subtotal');
    }

    public function submit()
    {
        $this->validate([
            'nome_cliente' => 'required|string|max:255',
            'telefone' => 'required|string|max:20',
            'tipo_pagamento' => 'required|string|in:PIX,DINHEIRO,CREDITO,DEBITO',
        ]);

        if ($this->totalCarrinho <= 0) {
            Notification::make()
                ->title('Erro: O total do carrinho não pode ser zero.')
                ->danger()
                ->send();
            return;
        }

        $venda = collect($this->carrinho)->every(fn($item) => $item['venda']);
        $encomenda = !$venda;

        $pagamento = Pagamento::create([
            'nome_cliente' => $this->nome_cliente,
            'telefone' => $this->telefone,
            'tipo_pagamento' => $this->tipo_pagamento,
            'valor' => $this->totalCarrinho,
            'observacao_pagamento' => $this->observacao_pagamento,
            'id_vendendor' => auth()->user()->id,
            'venda' => $venda,
            'encomenda' => $encomenda,
        ]);

        foreach ($this->carrinho as $item) {
            $produto = Produto::find($item['id']);
            $pagamento->produtos()->attach($produto->id, [
                'quantidade' => $item['quantidade'],
                'preco_unitario' => $item['preco_unitario'],
            ]);

            if ($item['venda']) {
                $produto->quantidade -= $item['quantidade'];
                $produto->save();

                Venda::create([
                    'id_pagamento' => $pagamento->id,
                    'aprovada' => true,
                ]);
            } else {
                Encomenda::create([
                    'id_pagamento' => $pagamento->id,
                    'aprovada' => false,
                ]);
            }
        }

        $this->reset(['carrinho', 'totalCarrinho']);
        Notification::make()
            ->title('Transação Concluída com Sucesso!')
            ->success()
            ->send();
    }

    public function render()
    {
        return view('livewire.carrinho', [
            'carrinho' => $this->carrinho,
            'totalCarrinho' => $this->totalCarrinho,
        ]);
    }
}
