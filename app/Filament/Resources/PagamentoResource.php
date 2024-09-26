<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PagamentoResource\Pages;
use App\Filament\Resources\PagamentoResource\RelationManagers;
use App\Models\Pagamento;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PagamentoResource extends Resource
{
    protected static ?string $model = Pagamento::class;

    protected static ?string $navigationIcon = 'heroicon-o-banknotes';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('id_produto')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('id_vendendor')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('nome_cliente')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('telefone')
                    ->tel()
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('tipo_pagamento')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('valor')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('desconto')
                    ->numeric(),
                Forms\Components\TextInput::make('quantidade')
                    ->required()
                    ->numeric(),
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
                Tables\Columns\TextColumn::make('produto.nome')
                    ->sortable(),
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Vendedor')
                    ->sortable(),
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
                Tables\Columns\TextColumn::make('quantidade')
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
                Tables\Columns\TextColumn::make('deleted_at')
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
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
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
            'index' => Pages\ListPagamentos::route('/'),
            //'create' => Pages\CreatePagamento::route('/create'),
            //'edit' => Pages\EditPagamento::route('/{record}/edit'),
        ];
    }
}
