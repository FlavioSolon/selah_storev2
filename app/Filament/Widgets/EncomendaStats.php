<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use App\Models\Encomenda;
class EncomendaStats extends BaseWidget
{
    protected function getCards(): array
    {
        return [
            Stat::make('Total de Encomendas Entregues', true)
                ->description('Entregues até o momento')
                ->value(Encomenda::where('entregue', true)->count())
                ->icon('heroicon-o-check-circle'),

            Stat::make('Total de Encomendas Aprovadas', true)
                ->description('Aprovadas até o momento')
                ->value(Encomenda::where('aprovada', true)->count())
                ->icon('heroicon-o-badge-check'),
        ];
    }
}
