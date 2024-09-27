<?php

namespace App\Filament\Resources\SummaryResource\Pages;

use App\Filament\Resources\SummaryResource;
use Filament\Resources\Pages\Page;

class Testing extends Page
{
    protected static string $resource = SummaryResource::class;

    protected static string $view = 'filament.resources.summary-resource.pages.testing';
}
