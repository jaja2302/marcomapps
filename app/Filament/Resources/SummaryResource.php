<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SummaryResource\Pages;
use App\Filament\Resources\SummaryResource\RelationManagers;
use App\Filament\Resources\SummaryResource\Widgets\GraphSummary;
use App\Filament\Resources\SummaryResource\Widgets\IncomeSummaryWidget;
use App\Filament\Resources\SummaryResource\Widgets\SampleTypeDistributionWidget;
use App\Models\Detailresi;
use App\Models\Summary;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class SummaryResource extends Resource
{
    protected static ?string $model = Detailresi::class;
    protected static ?string $navigationGroup = 'Dashboard';
    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationLabel = 'Summary';
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
            ]);
    }


    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                //
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
    public static function getWidgets(): array
    {
        return [
            GraphSummary::class,
            SampleTypeDistributionWidget::class,
            IncomeSummaryWidget::class,
        ];
    }
    public static function getPages(): array
    {
        return [
            // 'index' => Pages\ListSummaries::route('/'),
            'create' => Pages\CreateSummary::route('/create'),
            'edit' => Pages\EditSummary::route('/{record}/edit'),
            'sort' => Pages\Testing::route('/sort'),
        ];
    }
}
