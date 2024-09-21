<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DatabaseResource\Pages;
use App\Filament\Resources\DatabaseResource\RelationManagers;
use App\Filament\Widgets\Tracksampel;
use App\Models\Database;
use App\Models\Databaseinvoice;
use App\Models\JenisSampel;
use App\Models\ParameterAnalisis;
use App\Models\Perusahaan;
use Filament\Forms;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Pages\SubNavigationPosition;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Mail;
use Filament\Tables\Actions\Action;
use GuzzleHttp\Client;
use Filament\Tables\Columns\TextColumn;
use Filament\Resources\Pages\Page;

class DatabaseResource extends Resource
{
    protected static ?string $model = Databaseinvoice::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static SubNavigationPosition $subNavigationPosition = SubNavigationPosition::End;
    protected static ?string $modelLabel = 'invoice';
    protected static ?string $recordTitleAttribute = 'nama_pelanggan';
    protected static ?string $navigationLabel = 'invoice';
    protected static ?string $navigationGroup = 'Dashboard';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('nama_perusahaan')
                    ->label('Nama Perusahaan')
                    ->required()
                    ->live()
                    ->afterStateUpdated(function (Get $get, Set $set) {
                        // self::updateTotalharga($get, $set);
                    })
                    ->options(Perusahaan::query()->pluck('nama', 'id')->toArray()),
                TextInput::make('nama_pelanggan')->required()
                    ->live()
                    ->afterStateUpdated(function (Get $get, Set $set) {
                        // self::updateTotalharga($get, $set);
                    }),
                TextInput::make('no_group')->required()
                    ->live()
                    ->afterStateUpdated(function (Get $get, Set $set) {
                        // self::updateTotalharga($get, $set);
                    }),
                DateTimePicker::make('tanggal_sertifikat')->required()
                    ->live()
                    ->afterStateUpdated(function (Get $get, Set $set) {
                        // self::updateTotalharga($get, $set);
                    }),
                DateTimePicker::make('tanggal_penerbitan_invoice')->required()->live()
                    ->afterStateUpdated(function (Get $get, Set $set) {
                        // self::updateTotalharga($get, $set);
                    }),
                TextInput::make('tujuan_pengiriman')->required()
                    ->live()
                    ->afterStateUpdated(function (Get $get, Set $set) {
                        // self::updateTotalharga($get, $set);
                    }),
                TextInput::make('pembayaran')->required()
                    ->live()
                    ->afterStateUpdated(function (Get $get, Set $set) {
                        // self::updateTotalharga($get, $set);
                    }),
                DateTimePicker::make('tanggal_pembayaran')->required(),
                Section::make('Sample Details')
                    ->description('Please double-check all data before submitting to the system!')
                    ->schema([
                        Repeater::make('letterDetails')
                            ->label('Detail Nomor Surat')
                            ->schema([
                                TextInput::make('letterNumber')
                                    ->label('No surat')
                                    ->required(),
                                Repeater::make('locationDetails')
                                    ->label('Detail Lokasi')
                                    ->schema([
                                        TextInput::make('location')
                                            ->label('Lokasi')
                                            ->placeholder('Tidak Wajib di isi dapat di kosongkan saja')
                                            // ->required()
                                            ->columnSpanFull(),
                                        Repeater::make('parameterDetails')
                                            ->label('Detail Parameter')
                                            ->schema([
                                                Select::make('sampleType')
                                                    ->label('Jenis Sampel')
                                                    ->options(JenisSampel::query()->where('soft_delete_id', '!=', 1)->pluck('nama', 'id'))
                                                    ->required()
                                                    ->live(debounce: 500),
                                                Select::make('parameter')
                                                    ->label('Parameter')
                                                    ->options(fn(Get $get) => ParameterAnalisis::where('id_jenis_sampel', $get('sampleType'))->pluck('nama_parameter', 'id')->toArray())
                                                    ->afterStateUpdated(function ($set, $state) {
                                                        $params = ParameterAnalisis::find($state);
                                                        $set('parameterData', $params->nama_unsur);
                                                        $set('samplePrice', $params->harga);
                                                        $set('totalPrice', $params->harga);
                                                    })
                                                    ->required()
                                                    ->disableOptionsWhenSelectedInSiblingRepeaterItems()
                                                    ->live(debounce: 500),
                                                TextInput::make('sampleCount')
                                                    ->label('Jumlah Sampel')
                                                    ->numeric()
                                                    ->required()
                                                    ->minValue(1)
                                                    ->maxValue(1000)
                                                    ->disabled(fn($get) => is_null($get('parameterData')))
                                                    ->afterStateUpdated(function (Get $get, Set $set) {
                                                        self::updateTotals($get, $set);
                                                    })
                                                    ->live(debounce: 500),
                                                TextInput::make('parameterData')
                                                    ->label('Parameter Data')
                                                    ->readOnly()
                                                    ->disabled(fn($get) => is_null($get('parameterData'))),
                                                TextInput::make('samplePrice')
                                                    ->label('Harga')
                                                    ->disabled(fn($get) => is_null($get('parameterData')))
                                                    ->readOnly(),
                                                TextInput::make('subtotal')
                                                    ->label('Subtotal')
                                                    ->readOnly()
                                                    ->afterStateHydrated(function (Get $get, Set $set) {
                                                        self::updateTotals($get, $set);
                                                    })
                                                    ->disabled(fn($get) => is_null($get('parameterData')))
                                            ])
                                            ->addActionLabel('Tambah Parameter baru')
                                            ->columnSpanFull()
                                    ])
                                    ->addActionLabel('Tambah Lokasi baru')
                                    ->columnSpanFull()
                            ])
                            ->addActionLabel('Tambah Nomor surat baru')
                            ->columnSpanFull()
                    ])
                    ->columnSpanFull(),
            ]);
    }
    public static function updateTotals(Get $get, Set $set): void
    {

        $selectedProducts = $get('sampleCount');
        $harga = $get('totalPrice');
        // if ($selectedProducts != null) {
        //     dd($selectedProducts, $harga);
        // }


        // dd($selectedProducts);

        // Calculate subtotal based on the selected products and quantities
        $subtotal = $harga * $selectedProducts;

        // Update the state with the new values
        $set('subtotal', $subtotal);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->query(Databaseinvoice::query())
            ->columns([
                TextColumn::make('Perusahaan.nama')
                    ->sortable()
                    ->size('xs'),
                TextColumn::make('nama_pelanggan')
                    ->sortable()
                    ->size('xs'),
                TextColumn::make('no_group')
                    ->sortable()
                    ->size('xs'),
                TextColumn::make('tanggal_sertifikat')
                    ->sortable()
                    ->size('xs'),
                TextColumn::make('tanggal_penerbitan_invoice')
                    ->sortable()
                    ->size('xs'),
                TextColumn::make('resi_pengiriman')
                    ->sortable()
                    ->size('xs'),
                TextColumn::make('tujuan_pengiriman')
                    ->sortable()
                    ->size('xs'),
                TextColumn::make('status_pembayaran')
                    ->sortable()
                    ->size('xs'),
                TextColumn::make('tanggal_pembayaran')
                    ->sortable()
                    ->size('xs'),
            ])
            ->actions([
                Action::make('download_invoice')
                    ->label('Download Invoice')
                    ->action(function (Databaseinvoice $record) {
                        $client = new Client();
                        // dd($record);
                        if ($record->id !== null) {
                            // Make a GET request to the API with query parameters
                            $response = $client->get('https://management.srs-ssms.com/api/invoices_smartlabs', [
                                'query' => [
                                    'email' => 'j',
                                    'password' => 'j',
                                    'id_data' => $record->id,
                                ],
                            ]);

                            $responseData = json_decode($response->getBody()->getContents(), true);

                            if (isset($responseData['pdf'])) {
                                // Decode the base64 PDF
                                $pdfContent = base64_decode($responseData['pdf']);
                                $pdfFilename = $responseData['filename'];

                                // Return the PDF as a download
                                return response()->streamDownload(function () use ($pdfContent) {
                                    echo $pdfContent;
                                }, $pdfFilename, [
                                    'Content-Type' => 'application/pdf',
                                    'Content-Disposition' => 'attachment; filename="' . $pdfFilename . '"',
                                ]);
                            }
                        }

                        return Notification::make()
                            ->success()
                            ->title('Success')
                            ->body('Invoice berhasil diunduh')
                            ->send();
                    })
                    ->icon('heroicon-o-document-chart-bar')
                    ->color('success')
                    ->size('xs')
            ]);
    }

    public static function getRecordSubNavigation(Page $page): array
    {
        return $page->generateNavigationItems([
            Pages\ListDatabases::class,
            Pages\ListDatabases::class,
        ]);
    }
    public static function getPages(): array
    {
        return [
            'index' => Pages\ListDatabases::route('/'),
            'create' => Pages\CreateDatabase::route('/create'),
            // 'edit' => Pages\EditDatabase::route('/{record}/edit'),
        ];
    }
}
