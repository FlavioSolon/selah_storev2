<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProdutoResource\Pages;
use App\Filament\Resources\ProdutoResource\RelationManagers;
use App\Models\Produto;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\ColorPicker;
use Filament\Tables\Columns\ColorColumn;


class ProdutoResource extends Resource
{
    protected static ?string $model = Produto::class;

    protected static ?string $navigationIcon = 'heroicon-o-shopping-cart';

    public static function form(Form $form): Form
    {
        return $form
        ->schema([
            Forms\Components\TextInput::make('nome')
            ->required()
            ->maxLength(255)
            ->label('Nome do Produto')
            ->columnSpan('full')
            ->extraAttributes(['class' => 'my-2']), // Adiciona margem vertical para espaçamento

        Forms\Components\TextInput::make('preco')
            ->required()
            ->numeric()
            ->step(0.01)
            ->label('Preço')
            ->prefix('R$')
            ->extraAttributes(['class' => 'my-2']), // Adiciona margem vertical para espaçamento

        Forms\Components\TextInput::make('quantidade')
            ->required()
            ->numeric()
            ->label('Quantidade em Estoque')
            ->extraAttributes(['class' => 'my-2']), // Adiciona margem vertical para espaçamento

        Forms\Components\Select::make('tipo')
            ->label('Tipo de Produto')
            ->options([
                'Camisa' => 'Camisa',
                'Ecobag' => 'Ecobag',
            ])
            ->required()
            ->extraAttributes(['class' => 'my-2']), // Adiciona margem vertical para espaçamento

        Forms\Components\TextInput::make('modelo')
            ->maxLength(255)
            ->label('Modelo')
            ->extraAttributes(['class' => 'my-2']), // Adiciona margem vertical para espaçamento

        ColorPicker::make('cor')
            ->label('Cor')
            ->rgb()
            ->required()
            ->extraAttributes(['class' => 'my-2']), // Adiciona margem vertical para espaçamento

        Forms\Components\Select::make('tamanho')
            ->label('Tamanho')
            ->options([
                'PP' => 'PP',
                'P' => 'P',
                'M' => 'M',
                'G' => 'G',
                'GG' => 'GG',
                'XG' => 'XG',
            ])
            ->required()
            ->extraAttributes(['class' => 'my-2']), // Adiciona margem vertical para espaçamento

        Forms\Components\Toggle::make('em_estoque')
            ->label('Em Estoque')
            ->default(true)
            ->extraAttributes(['class' => 'my-4']),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
        ->columns([
            Tables\Columns\TextColumn::make('nome')
                ->sortable()
                ->searchable()
                ->label('Nome')
                ->extraAttributes(['style' => 'padding-right: 20px;']),

            Tables\Columns\TextColumn::make('preco')
                ->sortable()
                ->label('Preço')
                ->formatStateUsing(fn (string $state): string => 'R$ ' . number_format((float) $state, 2, ',', '.'))
                ->extraAttributes(['style' => 'padding-right: 20px;']),

            Tables\Columns\TextInputColumn::make('quantidade')
                ->sortable()
                ->label('Quantidade')
                ->extraAttributes(['style' => 'padding-right: 20px;']),

            Tables\Columns\TextColumn::make('tipo')
                ->sortable()
                ->label('Tipo')
                ->extraAttributes(['style' => 'padding-right: 20px;']),

            ColorColumn::make('cor')
                ->copyable()
                ->copyMessage('Cor copiada!')
                ->copyMessageDuration(1500)
                ->sortable()
                ->label('Cor')
                ->extraAttributes(['style' => 'padding-right: 20px;']),

            Tables\Columns\TextColumn::make('tamanho')
                ->sortable()
                ->label('Tamanho')
                ->extraAttributes(['style' => 'padding-right: 20px;']),

            Tables\Columns\ToggleColumn::make('em_estoque')
                ->label('Em Estoque')
                ->sortable()
                ->extraAttributes(['style' => 'padding-right: 20px;']),

            Tables\Columns\TextColumn::make('created_at')
                ->dateTime('d/m/Y H:i')
                ->label('Criado em')
                ->sortable()
                ->extraAttributes(['style' => 'padding-right: 20px;']),

            Tables\Columns\TextColumn::make('updated_at')
                ->dateTime()
                ->sortable()
                ->toggleable(isToggledHiddenByDefault: true)
                ->extraAttributes(['style' => 'padding-right: 20px;']),

            Tables\Columns\TextColumn::make('deleted_at')
                ->dateTime()
                ->sortable()
                ->toggleable(isToggledHiddenByDefault: true)
                ->extraAttributes(['style' => 'padding-right: 20px;']),
            ])
            ->filters([
                Tables\Filters\TrashedFilter::make(),
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
            'index' => Pages\ListProdutos::route('/'),

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
