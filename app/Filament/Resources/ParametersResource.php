<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ParametersResource\Pages;
use App\Filament\Resources\ParametersResource\Pages\CreateParameters;
use App\Filament\Resources\ParametersResource\RelationManagers;
use App\Models\ParameterAnalisis;
use App\Models\Parameters;
use App\Models\TrackParameter;
use Filament\Forms;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\TextInputColumn;
use Filament\Resources\Pages\Page;
use Filament\Pages\SubNavigationPosition;
use Filament\Tables\Filters\SelectFilter;

class ParametersResource extends Resource
{
    protected static ?string $model = ParameterAnalisis::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationGroup = 'Database';
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('nama_parameter')
            ]);
    }
    protected static SubNavigationPosition $subNavigationPosition = SubNavigationPosition::Top;
    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextInputColumn::make('nama_parameter')
                    ->label('Nama Parameter')
                    ->searchable()
                    ->rules(['required', 'max:255'])
                    ->visible(can_edit_invoice())
                    ->sortable(),
                TextColumn::make('nama_parameter_dua')
                    ->label('Nama Parameter')
                    ->searchable()
                    ->visible(!can_edit_invoice())
                    ->state(function ($record) {
                        return $record->nama_parameter;
                    })
                    ->sortable(),
                TextInputColumn::make('nama_unsur')
                    ->label('Nama Unsur')
                    ->searchable()
                    ->visible(can_edit_invoice())
                    ->rules(['required', 'max:255'])
                    ->sortable(),
                TextColumn::make('nama_unsur_dua')
                    ->label('Nama Unsur')
                    ->searchable()
                    ->visible(!can_edit_invoice())
                    ->state(function ($record) {
                        return $record->nama_unsur;
                    })
                    ->sortable(),
                TextInputColumn::make('bahan_produk')
                    ->label('Bahan Produk')
                    ->searchable()
                    ->visible(can_edit_invoice())
                    ->sortable(),
                TextColumn::make('bahan_produk_dua')
                    ->label('Bahan Produk')
                    ->searchable()
                    ->visible(!can_edit_invoice())
                    ->state(function ($record) {
                        return $record->bahan_produk;
                    })
                    ->sortable(),
                TextInputColumn::make('metode_analisis')
                    ->label('Metode Analisis')
                    ->searchable()
                    ->visible(can_edit_invoice())
                    ->rules(['required', 'max:255'])
                    ->sortable(),
                TextColumn::make('metode_analisis_dua')
                    ->label('Metode Analisis')
                    ->searchable()
                    ->visible(!can_edit_invoice())
                    ->state(function ($record) {
                        return $record->metode_analisis;
                    })
                    ->sortable(),
                TextInputColumn::make('harga')
                    ->label('Harga')
                    ->searchable()
                    ->visible(can_edit_invoice())
                    ->rules(['required', 'int'])
                    ->sortable(),
                TextColumn::make('harga_dua')
                    ->label('Harga')
                    ->searchable()
                    ->visible(!can_edit_invoice())
                    ->state(function ($record) {
                        return $record->harga;
                    })
                    ->sortable(),
                TextInputColumn::make('satuan')
                    ->label('Satuan')
                    ->visible(can_edit_invoice())
                    ->searchable()
                    ->rules(['required', 'max:255'])
                    ->sortable(),
                TextColumn::make('satuan_dua')
                    ->label('Satuan')
                    ->searchable()
                    ->visible(!can_edit_invoice())
                    ->state(function ($record) {
                        return $record->satuan;
                    })
                    ->sortable(),
                TextColumn::make('jenisSampel.nama')
                    ->label('Jenis Sampel')
                    ->searchable()
                    ->sortable()->size('xs'),
            ])
            ->filters([
                SelectFilter::make('id_jenis_sampel')
                    ->relationship('jenisSampel', 'nama')
                    ->multiple()
                    ->preload()
            ])
            ->actions([
                // Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()->visible(can_edit_invoice()),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }
    public static function getRecordSubNavigation(Page $page): array
    {
        return $page->generateNavigationItems([
            CreateParameters::class,
        ]);
    }
    public static function getPages(): array
    {
        return [
            'index' => Pages\ListParameters::route('/'),
            // 'create' => Pages\CreateParameters::route('/create'),
            // 'create-nonpaket' => Pages\Parameternonpaket::route('/create/nonpaket'),
            // 'edit' => Pages\EditParameters::route('/{record}/edit'),
        ];
    }
}
