<?php

namespace App\Filament\Widgets;

use Filament\Widgets\Widget;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Illuminate\Contracts\View\View;

class Inputdataform extends Widget implements HasForms
{
    use InteractsWithForms;

    protected static string $view = 'filament.widgets.inputdataform';

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill();
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('title')
                    ->required(),
                MarkdownEditor::make('content'),
                // ...
            ])
            ->statePath('data');
    }

    public function create(): void
    {
        dd($this->form->getState());
    }

    // public function render(): View
    // {
    //     return view('livewire.create-post');
    // }
}
