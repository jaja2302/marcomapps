<?php

namespace App\Filament\Resources\ParametersResource\Pages;

use App\Filament\Resources\ParametersResource;
use Filament\Actions;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Pages\CreateRecord;
use Filament\Forms\Form;

class Parameternonpaket extends CreateRecord
{
    protected static string $resource = ParametersResource::class;

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('nama_kon')
            ]);
    }
}
