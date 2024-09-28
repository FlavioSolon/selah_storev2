<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PagamentoResource\Pages;
use App\Models\Pagamento;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class PagamentoResource extends Resource
{
    protected static ?string $model = Pagamento::class;

    protected static ?string $navigationIcon = 'heroicon-o-banknotes';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('nome_cliente')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('telefone')
                    ->tel()
                    ->required()
                    ->maxLength(20),
                Forms\Components\TextInput::make('tipo_pagamento')
                    ->required()
                    ->maxLength(50),
                Forms\Components\TextInput::make('valor')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('desconto')
                    ->numeric(),
                Forms\Components\TextInput::make('produtos_com_variantes')
                    ->label('Produtos e Variantes')
                    ->default(fn (Pagamento $record) => $record ? $record->getProdutosComVariantesAttribute() : null)
                    ->disabled()
                    ->columnSpanFull(),
                Forms\Components\Toggle::make('venda')
                    ->required(),
                Forms\Components\Toggle::make('encomenda')
                    ->required(),
                Forms\Components\TextInput::make('observacao_pagamento')
                    ->maxLength(255),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nome_cliente')
                    ->searchable(),
                Tables\Columns\TextColumn::make('telefone')
                    ->searchable(),
                Tables\Columns\TextColumn::make('tipo_pagamento')
                    ->searchable(),
                Tables\Columns\TextColumn::make('valor')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('desconto')
                    ->numeric()
                    ->sortable(),

                Tables\Columns\IconColumn::make('venda')
                    ->boolean(),
                Tables\Columns\IconColumn::make('encomenda')
                    ->boolean(),
                Tables\Columns\TextColumn::make('observacao_pagamento')
                    ->searchable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
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
            'index' => Pages\ListPagamentos::route('/'),
            'create' => Pages\CreatePagamento::route('/create'),
            'edit' => Pages\EditPagamento::route('/{record}/edit'),
        ];
    }
}
