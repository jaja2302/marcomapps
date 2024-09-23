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
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
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
use Illuminate\Database\Eloquent\Model;
use Filament\Notifications\Actions\Action as Notifaction;

class DatabaseResource extends Resource
{
    protected static ?string $model = Databaseinvoice::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static SubNavigationPosition $subNavigationPosition = SubNavigationPosition::End;
    protected static ?string $modelLabel = 'invoice';
    protected static ?string $recordTitleAttribute = 'nama_pelanggan';
    protected static ?string $navigationLabel = 'invoice';
    protected static ?string $navigationGroup = 'Dashboard';
    public $e_materai_status = true;
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('nama_perusahaan')
                    ->label('Nama Perusahaan')
                    ->required()
                    ->live()
                    ->searchable()
                    ->relationship(name: 'perusahaan', titleAttribute: 'nama')
                    ->createOptionForm([
                        TextInput::make('nama')->required()->placeholder('Wajib diisi'),
                        TextInput::make('nama_pelanggan')->required()->placeholder('Wajib diisi'),
                        Textarea::make('alamat_pelanggan')->placeholder('Dapat dikosongkan tidak perlu diisi'),
                        Textarea::make('no_telp_perusahaan')->placeholder('Dapat dikosongkan tidak perlu diisi'),
                        Textarea::make('email_perusahaan')->placeholder('Dapat dikosongkan tidak perlu diisi'),
                        Textarea::make('npwp_perusahaan')->placeholder('Dapat dikosongkan tidak perlu diisi'),
                        Textarea::make('no_kontrak_perusahaan')->placeholder('Dapat dikosongkan tidak perlu diisi'),
                    ])

                    ->createOptionUsing(function (array $data): Model {
                        // dd('added');
                        $perusahaan = Perusahaan::create([
                            'nama' => $data['nama'],
                            'nama_pelanggan' => $data['nama_pelanggan'],
                            'alamat_pelanggan' => $data['alamat_pelanggan'],
                            'no_telp_perusahaan' => $data['no_telp_perusahaan'],
                            'email_perusahaan' => $data['email_perusahaan'],
                            'npwp_perusahaan' => $data['npwp_perusahaan'],
                            'no_kontrak_perusahaan' => $data['no_kontrak_perusahaan'],
                        ]);


                        Notification::make()
                            ->title('Saved successfully')
                            ->success()
                            ->body('Harap klik tombol refresh untuk melihat data baru ditambahkan')
                            ->actions([
                                Notifaction::make('refresh')
                                    ->button()
                                    ->url('/admin/databases/create'), // Change this line to a string URL
                            ])
                            ->persistent()
                            ->send();
                        return $perusahaan;
                    })
                    ->editOptionForm([
                        TextInput::make('id')->label('uuid')->readOnly(),
                        TextInput::make('nama')->required()->placeholder('Wajib diisi'),
                        TextInput::make('nama_pelanggan')->required()->placeholder('Wajib diisi'),
                        Textarea::make('alamat_pelanggan')->placeholder('Dapat dikosongkan tidak perlu diisi'),
                        Textarea::make('no_telp_perusahaan')->placeholder('Dapat dikosongkan tidak perlu diisi'),
                        Textarea::make('email_perusahaan')->placeholder('Dapat dikosongkan tidak perlu diisi'),
                        Textarea::make('npwp_perusahaan')->placeholder('Dapat dikosongkan tidak perlu diisi'),
                        Textarea::make('no_kontrak_perusahaan')->placeholder('Dapat dikosongkan tidak perlu diisi'),
                    ])
                    ->updateOptionUsing(function (array $data): ?Model {
                        // dd($data);
                        $record = Perusahaan::find($data['id']);
                        $record->update([
                            'nama' => $data['nama'],
                            'nama_pelanggan' => $data['nama_pelanggan'],
                            'alamat_pelanggan' => $data['alamat_pelanggan'],
                            'no_telp_perusahaan' => $data['no_telp_perusahaan'],
                            'email_perusahaan' => $data['email_perusahaan'],
                            'npwp_perusahaan' => $data['npwp_perusahaan'],
                            'no_kontrak_perusahaan' => $data['no_kontrak_perusahaan'],
                        ]);

                        Notification::make()
                            ->title('Data berhasil diperbarui')
                            ->success()
                            ->send();

                        return $record;
                    })
                    ->options(Perusahaan::query()->pluck('nama', 'id')->toArray())
                    ->afterStateUpdated(function ($state, Set $set) {
                        $data = Perusahaan::where('id', $state)->first();
                        $set('nama_pelanggan', $data->nama_pelanggan ?? '');
                        $set('alamat_pelanggan', $data->alamat_pelanggan  ?? '');
                        $set('no_telp_perusahaan', $data->no_telp_perusahaan ?? '');
                        $set('email_perusahaan', $data->email_perusahaan  ?? '');
                        $set('npwp_perusahaan', $data->npwp_perusahaan  ?? '');
                        $set('no_kontrak_perusahaan', $data->no_kontrak_perusahaan  ?? '');
                    }),
                TextInput::make('nama_pelanggan')->readOnly()->placeholder('Otomatis dari sistem'),
                TextInput::make('alamat_pelanggan')->readOnly()->placeholder('Otomatis dari sistem'),
                TextInput::make('no_telp_perusahaan')->readOnly()->placeholder('Otomatis dari sistem'),
                TextInput::make('email_perusahaan')->readOnly()->placeholder('Otomatis dari sistem'),
                TextInput::make('npwp_perusahaan')->readOnly()->placeholder('Otomatis dari sistem'),
                TextInput::make('no_kontrak_perusahaan')->readOnly()->placeholder('Otomatis dari sistem'),
                TextInput::make('no_group')->required(),
                TextInput::make('no_sertifikat')->required(),
                DatePicker::make('tanggal_sertifikat')->required()->format('d/m/Y')->default(now()),
                DatePicker::make('tanggal_pengiriman_sertifikat')->required()->format('d/m/Y')->default(now()),
                DatePicker::make('tanggal_penerbitan_invoice')->required()->format('d/m/Y')->default(now()),
                DatePicker::make('tanggal_pengiriman_invoice')->required()->format('d/m/Y')->default(now()),
                DatePicker::make('tanggal_pembayaran')->required()->format('d/m/Y')->default(now()),
                FileUpload::make('faktur_pajak')
                    ->directory('penerbitan_invoice')
                    ->openable()
                    ->columnSpanFull()
                    ->previewable(true)
                    ->acceptedFileTypes(['application/pdf']),
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
                    ->afterStateUpdated(function (Get $get, Set $set) {
                        self::updateTotalharga($get, $set);
                    })
                    ->columnSpanFull(),
                TextInput::make('discount_percentage')
                    ->label('Discount Percentage')
                    ->numeric()
                    ->required()
                    ->minValue(0)
                    ->maxValue(100)
                    ->placeholder('Enter discount percentage (0-100%)')
                    ->live(debounce: 500)
                    ->afterStateUpdated(function (Get $get, Set $set) {
                        self::updateTotalharga($get, $set);
                        if ($get('totalharga_disc') > 1000000) {
                            Notification::make()
                                ->title('E-materai diperlukan')
                                ->body('Total harga melebihi 1 juta, harap unggah e-materai')
                                ->danger()
                                ->send();
                        }
                    }),
                TextInput::make('pembayaran')->required()
                    ->live(debounce: 500)
                    ->placeholder('Harap diisi untuk mengupdate total harga')
                    ->afterStateUpdated(function (Get $get, Set $set) {
                        self::updateTotalharga($get, $set);
                    }),
                TextInput::make('totalharga')->label('Total Harga')->readOnly()->placeholder('Otomatis'),
                TextInput::make('totalharga_disc')->label('Total Harga + Diskon')->readOnly()->placeholder('Otomatis'),
                FileUpload::make('e_materai')
                    ->image()
                    ->imageEditor()
                    ->required(fn(Get $get) => $get('e_matare_status') ? false : true)
                    ->columnSpanFull()
                    ->hidden(fn(Get $get) => ($get('totalharga_disc') > 1000000) ? false : true),
                Toggle::make('e_matare_status')
                    ->label('Tambahkan E-materai nanti')
                    ->live(debounce: 500)
                    ->hidden(fn(Get $get) => ($get('totalharga_disc') > 1000000) ? false : true),
            ]);
    }
    public static function updateTotalharga(Get $get, Set $set): void
    {
        $total = 0;
        $letterDetails = $get('letterDetails') ?? [];

        foreach ($letterDetails as $letter) {
            $locationDetails = $letter['locationDetails'] ?? [];
            foreach ($locationDetails as $location) {
                $parameterDetails = $location['parameterDetails'] ?? [];
                foreach ($parameterDetails as $parameter) {
                    $total += $parameter['subtotal'] ?? 0;
                }
            }
        }
        $total_disc = $total;
        $discountPercentage = $get('discount_percentage');
        if ($discountPercentage != null) {
            $total_disc = $total - ($total * ($discountPercentage / 100));
        }
        // Toggle e-materai status
        $set('totalharga_disc', $total_disc);
        $set('totalharga', $total);
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
                TextColumn::make('Perusahaan.nama_pelanggan')
                    ->sortable()
                    ->label('Nama Pelanggan')
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
                TextColumn::make('Perusahaan.alamat_pelanggan')
                    ->label('Alamat Pelanggan')
                    ->sortable()
                    ->size('xs'),
                TextColumn::make('status_pembayaran')
                    ->sortable()
                    ->size('xs'),
                TextColumn::make('tanggal_pembayaran')
                    ->sortable()
                    ->size('xs'),
                TextColumn::make('e_materai_status')
                    ->sortable()
                    ->state(function (Databaseinvoice $record) {
                        if ($record->e_materai_status == 1 && $record->e_materai == null) {
                            return 'Harap Upload E-materai';
                        } elseif ($record->e_materai_status == 0 && $record->e_materai == null) {
                            return 'Tidak Memerlukan E-materai';
                        } elseif ($record->e_materai_status == 0 && $record->e_materai !== null) {
                            return 'E-materai sudah diupload';;
                        } else {
                            return 'Invalid Status';
                        }
                    })
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
