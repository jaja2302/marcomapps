<?php

namespace App\Filament\Resources\ParametersResource\Pages;

use App\Filament\Resources\ParametersResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Components\Tab;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\ParametersResource\Pages;
use App\Models\JenisSampel;
use App\Models\ParameterAnalisis;
use Filament\Resources\Pages\Page;
use Filament\Pages\SubNavigationPosition;
use Filament\Actions\Action;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Actions\CreateAction;
use Filament\Pages\Dashboard\Actions\FilterAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\DB;

class ListParameters extends ListRecords
{
    protected static string $resource = ParametersResource::class;
    public function getTabs(): array
    {
        return [
            'all' => Tab::make(),
            'Paket' => Tab::make()
                ->modifyQueryUsing(fn(Builder $query) => $query->where('jenis_paket', 'Paket')),
            'NonPaket' => Tab::make()
                ->modifyQueryUsing(fn(Builder $query) => $query->where('jenis_paket', 'Nonpaket')),
        ];
    }


    protected function getHeaderActions(): array
    {
        return [
            Action::make('JenisSampel')
                ->model(JenisSampel::class)
                ->form([
                    TextInput::make('nama')->placeholder('Nama Jenis Sampel')->required(),
                    TextInput::make('kode')->placeholder('Contoh Daun L')->required(),
                ])
                ->visible(can_edit_invoice())
                ->action(function (array $data, string $model) {
                    $parametersToInsert[] = [
                        'nama' => $data['nama'],
                        'kode' => $data['kode'],
                        'progress' => '1,2,3,4,5,6,7',
                    ];

                    try {
                        DB::beginTransaction();
                        $model::insert($parametersToInsert);
                        DB::commit();

                        Notification::make()
                            ->success()
                            ->title('Success')
                            ->body('Jenis Sampel successfully added')
                            ->send();

                        return new JenisSampel();
                    } catch (\Exception $e) {
                        DB::rollBack();

                        Notification::make()
                            ->danger()
                            ->title('Error')
                            ->color('danger')
                            ->body($e)
                            ->send();

                        return new JenisSampel();
                    }
                }),

            Action::make('NonPaket')
                ->model(ParameterAnalisis::class)
                ->form([
                    Repeater::make('members')
                        ->schema([
                            Select::make('jenis')
                                ->options(JenisSampel::query()->pluck('nama', 'id'))
                                ->required(),
                            TextInput::make('namaparameter')
                                ->required()
                                ->label('Nama Parameter')
                                ->maxLength(255),
                            TextInput::make('hargaparams')
                                ->required()
                                ->label('Harga Parameter')
                                ->numeric()
                                ->maxLength(255),
                            TextInput::make('namametode')
                                ->required()
                                ->label('Nama Metode')
                                ->maxLength(255),
                            TextInput::make('namasatuan')
                                ->required()
                                ->label('Nama Satuan')
                                ->maxLength(255),
                            TextInput::make('bahan_produk')
                                ->label('Bahan Produk')
                                ->maxLength(255),
                            TextInput::make('nama_unsur')
                                ->label('Nama Unsur')
                                ->maxLength(255),
                        ])
                        ->addActionLabel('Add Parameter')
                        ->columns(4)


                ])
                ->visible(can_edit_invoice())
                ->action(function (array $data, string $model): ParameterAnalisis {
                    $parametersToInsert = [];
                    foreach ($data as $key => $value) {
                        foreach ($value as $key1 => $value1) {
                            $parametersToInsert[] = [
                                'nama_parameter' => $value1['namaparameter'],
                                'metode_analisis' => $value1['namametode'],
                                'harga' => $value1['hargaparams'],
                                'satuan' => $value1['namasatuan'],
                                'nama_unsur' => (is_null($value1['nama_unsur']) ? $value1['namaparameter'] : $value1['nama_unsur']),
                                'bahan_produk' => $value1['bahan_produk'],
                                'id_jenis_sampel' => $value1['jenis'],
                            ];
                        }
                    }

                    try {
                        DB::beginTransaction();
                        $model::insert($parametersToInsert);
                        DB::commit();

                        Notification::make()
                            ->success()
                            ->title('Success')
                            ->body('Parameters successfully added')
                            ->send();

                        return new ParameterAnalisis();
                    } catch (\Exception $e) {
                        DB::rollBack();

                        Notification::make()
                            ->danger()
                            ->title('Error')
                            ->color('danger')
                            ->body($e)
                            ->send();

                        return new ParameterAnalisis();
                    }
                }),
            Action::make('Paket')
                ->model(ParameterAnalisis::class)
                ->form([
                    Repeater::make('members')
                        ->label('Tambah Paket')
                        ->schema([
                            Select::make('jenis_paketan')
                                ->options(JenisSampel::query()->pluck('nama', 'id'))
                                ->label('Jenis Parameter')
                                ->afterStateUpdated(function (Set $set, $state) {
                                    $params = ParameterAnalisis::where('id_jenis_sampel', $state)->where('jenis_paket', 'Nonpaket')->get();
                                    $test = ParameterAnalisis::where('id_jenis_sampel', $state)->where('jenis_paket', 'Nonpaket')->pluck('nama_parameter', 'id');


                                    $newparams = [];
                                    foreach ($params as $key => $value) {
                                        if ($value['nama_parameter'] === $value['nama_unsur']) {
                                            $nama = $value['nama_parameter'] . '(Rp-,' . $value['harga'] . ')';
                                        } else {
                                            $nama = $value['nama_parameter'] . '(' . $value['nama_unsur'] . ')' . '(Rp-,' . $value['harga'] . ')';
                                        }
                                        $newparams[$value['id']] = $nama;
                                    };
                                    // dd($newparams, $test);
                                    $set('datanamaparameter', $newparams);
                                    $set('hargaparams_paketan', 0);
                                })
                                ->required()
                                ->live(debounce: 500),
                            CheckboxList::make('namaparameter_paketan')
                                ->options(function (Get $get) {

                                    return $get('datanamaparameter');
                                })
                                ->label('Nama Parameter')
                                ->gridDirection('row')
                                ->searchable()
                                ->columnSpanFull()
                                ->disabled(function ($get) {
                                    return is_null($get('datanamaparameter'));
                                })
                                ->noSearchResultsMessage('Parameter yang anda cari tidak tersedia, Silahkan Input lebih dahulu Parameter Non Satuan untuk muncul.')
                                ->columns(6)
                                ->afterStateUpdated(function (Set $set, $state) {
                                    // dd($state);
                                    $total = ParameterAnalisis::wherein('id', $state)
                                        ->where('jenis_paket', 'Nonpaket')
                                        ->pluck('harga')
                                        ->sum();
                                    $params = ParameterAnalisis::wherein('id', $state)->where('jenis_paket', 'Nonpaket')->pluck('nama_unsur')->toArray();
                                    $namaunsur = implode(',', $params);
                                    // dd(implode(',', $params));
                                    $set('hargaparams_paketan', $total);
                                    $set('nama_unsur', $namaunsur);
                                })
                                ->required()
                                ->live(debounce: 500),
                            TextInput::make('hargaparams_paketan')
                                ->required()
                                ->label('Harga Parameter')
                                ->numeric()
                                ->maxLength(255),
                            TextInput::make('nama_unsur')
                                ->required()
                                ->label('Nama Unsur Parameter')
                                ->maxLength(255),
                        ])
                        ->columns(2)
                ])
                ->visible(can_edit_invoice())
                ->action(function (array $data, string $model): ParameterAnalisis {
                    $parametersToInsert = [];
                    foreach ($data as $key => $value) {
                        foreach ($value as $key1 => $value1) {
                            $params = ParameterAnalisis::wherein('id', $value1['namaparameter_paketan'])->where('jenis_paket', 'Nonpaket')->pluck('nama_unsur')->toArray();
                            // dd(implode('$', $params));
                            $parametersToInsert[] = [
                                'nama_parameter' => implode(',', $params),
                                'harga' => $value1['hargaparams_paketan'],
                                'id_jenis_sampel' => $value1['jenis_paketan'],
                                'nama_unsur' => $value1['nama_unsur'],
                                'jenis_paket' => 'Paket',
                                'paket_id' => implode('$', $value1['namaparameter_paketan']),
                            ];
                        }
                    }

                    try {
                        DB::beginTransaction();
                        $model::insert($parametersToInsert);
                        DB::commit();

                        Notification::make()
                            ->success()
                            ->title('Success')
                            ->body('Parameters successfully added')
                            ->send();

                        return new ParameterAnalisis();
                    } catch (\Exception $e) {
                        DB::rollBack();

                        Notification::make()
                            ->danger()
                            ->title('Error')
                            ->color('danger')
                            ->body($e)
                            ->send();

                        return new ParameterAnalisis();
                    }
                }),
        ];
    }
}
