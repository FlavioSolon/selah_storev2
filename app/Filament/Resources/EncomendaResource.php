<?php

namespace App\Filament\Resources;

use App\Filament\Resources\EncomendaResource\Pages;
use App\Filament\Resources\EncomendaResource\RelationManagers;
use App\Models\Encomenda;
use App\Models\Variante;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class EncomendaResource extends Resource
{
    protected static ?string $model = Encomenda::class;

    protected static ?string $navigationIcon = 'heroicon-o-truck';

    public static function form(Form $form): Form
{
    return $form
        ->schema([
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
                            $variante = Variante::find($produto->pivot->variante_id);
                            $varianteNome = $variante ? $variante->valor : 'Sem variante';
                            return "{$produto->nome} ({$varianteNome})";
                        })->implode(', ');
                    }
                    return 'Nenhum produto associado';
                }),

            Forms\Components\Toggle::make('entregue')
                ->label('Entregue')
                ->required(),

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

            Tables\Columns\IconColumn::make('entregue')
                ->label('Entregue')
                ->boolean()
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
            Tables\Filters\Filter::make('entregue')
                ->query(fn (Builder $query) => $query->where('entregue', true)),
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
            'index' => Pages\ListEncomendas::route('/'),
            'create' => Pages\CreateEncomenda::route('/create'),
            'edit' => Pages\EditEncomenda::route('/{record}/edit'),
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
