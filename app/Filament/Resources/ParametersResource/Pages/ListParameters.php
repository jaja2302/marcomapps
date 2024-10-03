<?php

namespace App\Filament\Resources\ParametersResource\Pages;

use App\Filament\Resources\ParametersResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Components\Tab;
use Illuminate\Database\Eloquent\Builder;

class ListParameters extends ListRecords
{
    protected static string $resource = ParametersResource::class;

    public function getTabs(): array
    {
        return [
            'all' => Tab::make(),
            'Daun' => Tab::make()
                ->modifyQueryUsing(fn(Builder $query) => $query->where('id_jenis_sampel', 1)),
            'Tanah' => Tab::make()
                ->modifyQueryUsing(fn(Builder $query) => $query->where('id_jenis_sampel', 2)),
        ];
    }
    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make('InputParameters')->label('Input Parameter'),
            Actions\CreateAction::make('cok')->label('cok'),
        ];
    }
}
