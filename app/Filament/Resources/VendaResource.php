<?php

namespace App\Filament\Resources;

use App\Filament\Resources\VendaResource\Pages;
use App\Filament\Resources\VendaResource\RelationManagers;
use App\Models\Venda;
use App\Models\Variante;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class VendaResource extends Resource
{
    protected static ?string $model = Venda::class;

    protected static ?string $navigationIcon = 'heroicon-o-currency-dollar';

    public static function form(Form $form): Form
{
    return $form
        ->schema([
            // Exibe o nome do cliente e detalhes do pagamento
            Forms\Components\TextInput::make('id_pagamento')
                ->label('ID do Pagamento')
                ->disabled(),

            // Exibe os produtos e variantes relacionados
            Forms\Components\Textarea::make('produtos_comprados')
                ->label('Produtos Comprados')
                ->disabled()
                ->default(static function ($record) {
                    if ($record && $record->pagamento) {
                        return $record->pagamento->produtos->map(function ($produto) {
                            // Buscar variante via tabela pivô
                            $variante = Variante::find($produto->pivot->variante_id);
                            $varianteNome = $variante ? $variante->valor : 'Sem variante';
                            return "{$produto->nome} ({$varianteNome})";
                        })->implode(', ');
                    }
                    return 'Nenhum produto associado';
                }),

            Forms\Components\Toggle::make('aprovada')
                ->label('Aprovada')
                ->required(),
        ]);
}


public static function table(Table $table): Table
{
    return $table
        ->columns([
            Tables\Columns\TextColumn::make('pagamento.nome_cliente')
                ->label('Cliente')
                ->sortable(),

            Tables\Columns\TextColumn::make('pagamento.valor')
                ->label('Valor')
                ->money('BRL')
                ->sortable(),

            Tables\Columns\IconColumn::make('aprovada')
                ->label('Aprovada')
                ->boolean()
                ->sortable(),

            Tables\Columns\TextColumn::make('created_at')
                ->label('Data')
                ->dateTime('d/m/Y H:i')
                ->sortable(),
        ])
        ->filters([
            Tables\Filters\Filter::make('aprovada')
                ->query(fn (Builder $query) => $query->where('aprovada', true)),
        ])
        ->actions([
            Tables\Actions\EditAction::make(),
        ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\ForceDeleteBulkAction::make(),
                    Tables\Actions\RestoreBulkAction::make(),
                ]),
            ]);
    }


    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListVendas::route('/'),
            'create' => Pages\CreateVenda::route('/create'),
            'edit' => Pages\EditVenda::route('/{record}/edit'),
        ];
    }



    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}
