<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ParametersResource\Pages;
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

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextInputColumn::make('nama_parameter')
                    ->label('Nama Parameter')
                    ->searchable()
                    ->rules(['required', 'max:255'])
                    ->sortable(),
                TextInputColumn::make('nama_unsur')
                    ->label('Nama Unsur')
                    ->searchable()
                    ->rules(['required', 'max:255'])
                    ->sortable(),
                TextInputColumn::make('bahan_produk')
                    ->label('Bahan Produk')
                    ->searchable()
                    ->sortable(),
                TextInputColumn::make('metode_analisis')
                    ->label('Metode Analisis')
                    ->searchable()
                    ->rules(['required', 'max:255'])
                    ->sortable(),
                TextInputColumn::make('harga')
                    ->label('Harga')
                    ->searchable()
                    ->rules(['required', 'int'])
                    ->sortable(),
                TextInputColumn::make('satuan')
                    ->label('Satuan')
                    ->searchable()
                    ->rules(['required', 'max:255'])
                    ->sortable(),
                TextColumn::make('jenisSampel.nama')
                    ->label('Jenis Sampel')
                    ->searchable()
                    ->sortable()->size('xs'),
            ])
            ->filters([
                //
            ])
            ->actions([
                // Tables\Actions\EditAction::make(),
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

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListParameters::route('/'),
            'create' => Pages\CreateParameters::route('/create'),
            'edit' => Pages\EditParameters::route('/{record}/edit'),
        ];
    }
}
