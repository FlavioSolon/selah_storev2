<div>
    <h3 class="text-lg font-bold mb-4">Carrinho de Compras</h3>

    <table class="table-auto w-full text-sm">
        <thead>
            <tr>
                <th class="text-left py-2">Produto</th>
                <th class="text-left py-2">Quantidade</th>
                <th class="text-left py-2">Subtotal</th>
                <th class="text-left py-2">Ações</th>
            </tr>
        </thead>
        <tbody>
            @forelse($carrinho as $index => $item)
                <tr>
                    <td>{{ $item['nome'] }} ({{ $item['variante'] }})</td>
                    <td>{{ $item['quantidade'] }}</td>
                    <td>R$ {{ number_format($item['subtotal'], 2, ',', '.') }}</td>
                    <td>
                        <x-filament::button color="danger" size="sm" wire:click="removerDoCarrinho({{ $index }})">Remover</x-filament::button>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" class="text-center py-4">Carrinho vazio</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="mt-4 text-right">
        <h4 class="text-lg font-bold">Total: R$ {{ number_format($totalCarrinho, 2, ',', '.') }}</h4>
    </div>

    <!-- Formulário de Pagamento -->
    <form wire:submit.prevent="submit">
        <div class="mt-4">
            <label for="nome_cliente" class="block text-sm font-medium text-gray-700">Nome do Cliente</label>
            <input type="text" id="nome_cliente" wire:model="nome_cliente" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500" />
        </div>

        <div class="mt-4">
            <label for="telefone" class="block text-sm font-medium text-gray-700">Telefone</label>
            <input type="text" id="telefone" wire:model="telefone" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500" />
        </div>

        <div class="mt-4">
            <label for="tipo_pagamento" class="block text-sm font-medium text-gray-700">Tipo de Pagamento</label>
            <select id="tipo_pagamento" wire:model="tipo_pagamento" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                <option value="PIX">PIX</option>
                <option value="DINHEIRO">Dinheiro</option>
                <option value="CREDITO">Crédito</option>
                <option value="DEBITO">Débito</option>
            </select>
        </div>

        <div class="mt-4">
            <label for="observacao_pagamento" class="block text-sm font-medium text-gray-700">Observação do Pagamento</label>
            <textarea id="observacao_pagamento" wire:model="observacao_pagamento" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500" rows="4"></textarea>
        </div>

        <div class="mt-6 text-right">
            <x-filament::button color="success" type="submit">
                Finalizar Compra
            </x-filament::button>
        </div>
    </form>
</div>
