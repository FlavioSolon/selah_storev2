<x-filament-panels::page>
    <div class="lg:flex lg:space-x-4">

        <!-- Formulário para Adicionar Produto -->
        <div class="lg:w-1/3 bg-white dark:bg-gray-800 shadow rounded-lg p-4">
            <h3 class="font-bold text-lg mb-4 text-center">Adicionar Produto</h3>
            <form wire:submit.prevent="adicionarCarrinho" class="space-y-4">
                <!-- Renderiza o formulário de adicionar produto -->
                {{ $this->form->render() }}

                <x-filament::button type="submit" class="w-full mt-4" color="primary">
                    Adicionar ao Carrinho
                </x-filament::button>
            </form>
        </div>

        <!-- Carrinho de Compras -->
        <div class="lg:w-1/3 bg-white dark:bg-gray-800 shadow rounded-lg p-4">
            <h3 class="font-bold text-lg mb-4 text-center">Carrinho de Compras</h3>

            @forelse($carrinho as $index => $item)
                <div class="flex justify-between mb-2 p-2 bg-gray-100 dark:bg-gray-700 rounded">
                    <div>
                        <!-- Exibe o nome do produto com suas variantes concatenadas -->
                        <p><strong>{{ $item['nome'] }}</strong></p>
                        <p>Qtd: {{ $item['quantidade'] }}</p>
                    </div>
                    <div class="text-right">
                        <p>R$ {{ number_format($item['subtotal'], 2, ',', '.') }}</p>
                        <x-filament::button type="button" color="danger" size="sm"
                                            wire:click="removerDoCarrinho({{ $index }})">
                            Remover
                        </x-filament::button>
                    </div>
                </div>
            @empty
                <p class="text-center text-gray-500">Carrinho vazio</p>
            @endforelse

            <div class="mt-4 text-right">
                <h4 class="font-bold">Total: R$ {{ number_format($totalCarrinho, 2, ',', '.') }}</h4>
            </div>
        </div>

        <!-- Formulário para Processar Pagamento -->
        <div class="lg:w-1/3 bg-white dark:bg-gray-800 shadow rounded-lg p-4">
            <h3 class="font-bold text-lg mb-4 text-center">Finalizar Pagamento</h3>

            <form wire:submit.prevent="submit" class="space-y-4">
                <!-- Campos de entrada para os dados do cliente -->
                <div>
                    <label for="nome_cliente" class="block text-sm font-medium">Nome do Cliente</label>
                    <input type="text" id="nome_cliente" wire:model="nome_cliente" class="mt-1 block w-full border-gray-300 dark:bg-gray-700 dark:text-gray-200 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                </div>

                <div>
                    <label for="telefone" class="block text-sm font-medium">Telefone</label>
                    <input type="text" id="telefone" wire:model="telefone" class="mt-1 block w-full border-gray-300 dark:bg-gray-700 dark:text-gray-200 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                </div>

                <div>
                    <label for="tipo_pagamento" class="block text-sm font-medium">Tipo de Pagamento</label>
                    <select id="tipo_pagamento" wire:model="tipo_pagamento" class="mt-1 block w-full border-gray-300 dark:bg-gray-700 dark:text-gray-200 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                        <option value="">Selecione o pagamento</option> <!-- Adicione uma opção vazia para forçar seleção -->
                        <option value="PIX">PIX</option>
                        <option value="DINHEIRO">Dinheiro</option>
                        <option value="CREDITO">Crédito</option>
                        <option value="DEBITO">Débito</option>
                    </select>
                </div>

                <div>
                    <label for="observacao_pagamento" class="block text-sm font-medium">Observações</label>
                    <textarea id="observacao_pagamento" wire:model="observacao_pagamento" class="mt-1 block w-full border-gray-300 dark:bg-gray-700 dark:text-gray-200 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"></textarea>
                </div>

                <x-filament::button type="submit" color="success" class="w-full mt-4">
                    Finalizar Pagamento
                </x-filament::button>
            </form>
        </div>
    </div>
</x-filament-panels::page>
