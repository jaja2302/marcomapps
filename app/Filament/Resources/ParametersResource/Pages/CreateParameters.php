<?php

namespace App\Filament\Resources\ParametersResource\Pages;

use App\Filament\Resources\ParametersResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Filament\Actions\Action;

class CreateParameters extends CreateRecord
{
    protected static string $resource = ParametersResource::class;


    protected function getFormActions(): array
    {
        return [
            ...parent::getFormActions(),
            Action::make('close')->action('createAndClose'),
        ];
    }

    public function createAndClose(): void
    {
        // ...
    }
}
