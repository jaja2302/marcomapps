<?php

namespace App\Filament\Resources\DatabaseResource\Pages;

use App\Filament\Resources\DatabaseResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListDatabases extends ListRecords
{
    protected static string $resource = DatabaseResource::class;

    protected function getHeaderActions(): array
    {
        // dd(can_edit_invoice());
        return [
            Actions\CreateAction::make()
                ->label('Tambah Invoice')
                ->visible(can_edit_invoice())
                ->createAnother(false),
        ];
    }
}
