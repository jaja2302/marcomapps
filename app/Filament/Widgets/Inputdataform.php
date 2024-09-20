<?php

namespace App\Filament\Widgets;

use App\Models\Databaseinvoice;
use App\Models\Detailresi;
use App\Models\JenisAnalisa;
use App\Models\JenisSampel;
use App\Models\ParameterAnalisis;
use App\Models\Pengguna;
use App\Models\Perusahaan;
use App\Models\Progress;
use App\Models\Tracksampel;
use Carbon\Carbon;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Grid;
use Filament\Widgets\Widget;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Illuminate\Contracts\View\View;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Split;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

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
            ])
            ->statePath('data');
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

    public function create(): void
    {
        try {
            // Wrapping in a transaction using a model
            DB::transaction(function () {
                // Debugging the form state
                $form = $this->form->getState();
                $query = new Databaseinvoice();
                $randomString = Str::upper(Str::random(10)); // Generates a random 10 character string
                $randomNumber = rand(1000, 9999); // Generates a 4-digit number
                $details_record = Carbon::now()->format('Ymd') . $query->id_jenis_sampel . $randomString . $randomNumber;

                // Mapping form data to the database columns
                $query->perusahaan_id = $form['nama_perusahaan'];
                $query->nama_pelanggan = $form['nama_pelanggan'];
                $query->no_group = $form['no_group'];
                $query->tanggal_sertifikat = $form['tanggal_sertifikat'];
                $query->tanggal_penerbitan_invoice = $form['tanggal_penerbitan_invoice'];
                $query->tujuan_pengiriman = $form['tujuan_pengiriman'];
                $query->status_pembayaran = $form['pembayaran'];
                $query->tanggal_pembayaran = $form['tanggal_pembayaran'];
                $query->resi_pengiriman = $details_record;

                // Save the main invoice query
                $query->save();

                // Creating detail resi entry
                $dataresi = new Detailresi();
                $dataresi->resi_id = $details_record;
                $dataresi->data = json_encode($form['letterDetails']);
                $dataresi->save();

                // If both saves are successful, commit happens automatically
                Notification::make()
                    ->title('Data Berhasil Disimpan')
                    ->success()
                    ->send();

                $this->form->fill();
            }, 5); // Retry the transaction 5 times in case of deadlock
        } catch (\Exception $e) {
            // Rollback happens automatically, so you can just handle the exception
            Notification::make()
                ->title('Data Gagal Disimpan')
                ->danger()
                ->send();
        }
    }

    // public function render(): View
    // {
    //     return view('livewire.create-post');
    // }
}
