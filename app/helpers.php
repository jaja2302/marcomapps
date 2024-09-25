<?php

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
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Model;
use Filament\Notifications\Actions\Action as Notifaction;

if (!function_exists('tanggal_indo')) {
    function tanggal_indo($tanggal, $cetak_hari = false, $cetak_bulan = false, $cetak_tanggal = false)
    {
        $hari = array(
            1 => 'Senin',
            'Selasa',
            'Rabu',
            'Kamis',
            'Jumat',
            'Sabtu',
            'Minggu'
        );

        $bulan = array(
            1 => 'Januari',
            'Februari',
            'Maret',
            'April',
            'Mei',
            'Juni',
            'Juli',
            'Agustus',
            'September',
            'Oktober',
            'November',
            'Desember'
        );
        $split = explode('-', $tanggal);
        $splitted_tgl_jam = explode(' ', $split[2]);

        $tgl_indo = $splitted_tgl_jam[0] . ' ' . $bulan[(int)$split[1]] . ' ' . $split[0] . ', ' . $splitted_tgl_jam[1];

        if ($cetak_hari) {
            $num = date('N', strtotime($tanggal));
            return $hari[$num] . ', ' . $tgl_indo;
        }

        if ($cetak_bulan) {
            return $bulan[(int)$split[1]] . ' ' . $split[0];
        }

        if ($cetak_tanggal) {
            return $splitted_tgl_jam[0] . ' ' . $bulan[(int)$split[1]] . ' ' . $split[0];
        }
        return $tgl_indo;
    }
}

if (!function_exists('formatNumber')) {
    function formatNumber($number)
    {
        return number_format($number, 0, ',', '.');
    }
}

if (!function_exists('generateRandomString')) {
    function generateRandomString($length)
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $randomString = '';

        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, strlen($characters) - 1)];
        }

        return $randomString;
    }
}

if (!function_exists('hitungPPN')) {
    function hitungPPN($angka)
    {
        return ($angka * 11) / 100;
    }
}
if (!function_exists('numberformat')) {
    function numberformat($number)
    {
        // Remove any non-numeric characters from the input number
        $number = preg_replace('/\D/', '', $number);

        // Check if the number starts with '0'
        if (strpos($number, '0') === 0) {
            // Replace '0' with '62'
            return '62' . substr($number, 1);
        } else if (strpos($number, '8') === 0) {
            // Replace '0' with '62'
            return '62' . $number;
        } else {
            // If it doesn't start with '0', return as is
            return $number;
        }
    }
}


if (!function_exists('array_email')) {
    function array_email($input)
    {
        $delimiters = [";", ",", " "];
        $emailArray = preg_split('/[' . implode('', $delimiters) . ']/', $input, -1, PREG_SPLIT_NO_EMPTY);

        // Trim each email address to remove any leading or trailing whitespaces
        $emailArray = array_map('trim', $emailArray);

        // Filter out invalid email addresses
        $emailArray = array_filter($emailArray, function ($email) {
            return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
        });

        return $emailArray;
    }
}

if (!function_exists('generateRandomCode')) {
    function generateRandomCode($length = 8)
    {
        $characters = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ'; // Alphanumeric characters
        $code = '';

        // Generate a random code of the specified length
        for ($i = 0; $i < $length; $i++) {
            $code .= $characters[rand(0, strlen($characters) - 1)];
        }

        return $code;
    }
}


if (!function_exists('formatPhoneNumber')) {
    function formatPhoneNumber($phoneNumber)
    {
        // Remove any non-numeric characters from the phone number
        $phoneNumber = preg_replace('/\D/', '', $phoneNumber);

        // Check if the phone number starts with "8" followed by other digits
        if (preg_match('/^8\d+$/', $phoneNumber)) {
            $phoneNumber = '0' . $phoneNumber;
        }
        // Check if the phone number starts with "62" followed by other digits
        elseif (preg_match('/^62\d+$/', $phoneNumber)) {
            $phoneNumber = '0' . substr($phoneNumber, 2);
        }

        return $phoneNumber;
    }
}
if (!function_exists('formatLabNumber')) {
    function formatLabNumber($number)
    {
        if ($number >= 1000) {
            return number_format($number / 1000, 3, '.', '');
        } else {
            return $number;
        }
    }
}
if (!function_exists('incrementVersion')) {
    function incrementVersion($string)
    {
        // Extract the numeric part using a regular expression
        preg_match('/(\d+\.\d+)-(\d+)\.(\d+)/', $string, $matches);

        // Increment the last number
        $matches[3] += 1;

        // Rebuild the string with the incremented number
        return "FR-{$matches[1]}-{$matches[2]}.{$matches[3]}";
    }
}

if (!function_exists('incrementVersion_identitas')) {
    function incrementVersion_identitas($string)
    {
        // Extract the numeric parts using a regular expression
        preg_match('/FR-(\d+\.\d+)-(\d+\.\d+)-(\d+)/', $string, $matches);

        // Increment the last number
        $matches[3] += 1;

        // Rebuild the string with the incremented number
        return "FR-{$matches[1]}-{$matches[2]}-{$matches[3]}";
    }
}

if (!function_exists('numberformat_excel')) {
    function numberformat_excel($number)
    {
        // Remove any non-numeric characters from the input number
        $number = preg_replace('/\D/', '', $number);

        // Check if the number starts with '08'
        if (substr($number, 0, 2) === '08') {
            $number = '628' . substr($number, 2);
        }

        // Validate if the number starts with '628'
        if (substr($number, 0, 3) !== '628') {
            return "Error";
        }

        // Validate the length of the number
        $length = strlen($number);
        if ($length < 10 || $length > 15) {
            return "Error";
        }

        return $number;
    }
}
if (!function_exists('encryptInt')) {
    function encryptInt($integer, $key, $iv)
    {
        $data = strval($integer); // Convert integer to string
        $encrypted = openssl_encrypt($data, 'AES-128-CBC', $key, 0, $iv);
        return $encrypted;
    }
}

if (!function_exists('decryptInt')) {
    function decryptInt($encrypted, $key, $iv)
    {
        $decrypted = openssl_decrypt($encrypted, 'AES-128-CBC', $key, 0, $iv);
        return intval($decrypted); // Convert back to integer
    }
}
class InvoiceHelper
{
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
        $discon = 0;
        $discountPercentage = $get('discount_percentage');
        if ($discountPercentage != null) {
            $total_disc = $total - ($total * ($discountPercentage / 100));
            $discon = $total * ($discountPercentage / 100);
        }
        $ppn_Percentage = $get('ppn_percentage');
        $ppn = 0;
        // dd($ppn_Percentage);
        if ($ppn_Percentage != null) {
            $total_disc_ppn = $total_disc + ($total_disc * ($ppn_Percentage / 100));
            $ppn = $total_disc * ($ppn_Percentage / 100);
        }
        // Set updated values
        $set('totalharga_disc', $total_disc);
        $set('totalharga_ppn_disc', $total_disc_ppn);
        $set('ppn', $ppn);
        $set('discon', $discon);
        $set('subtotal', $total);
    }

    public static function updateTotals(Get $get, Set $set): void
    {
        $selectedProducts = $get('sampleCount');
        $harga = $get('totalPrice');
        $subtotal = $harga * $selectedProducts;

        // Update the state with the new values
        $set('subtotal', $subtotal);
    }
}
if (!function_exists('form_invoice')) {
    function form_invoice()
    {

        return [
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
                    Textarea::make('status_pajak')->placeholder('Dapat dikosongkan tidak perlu diisi'),
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
                        'status_pajak' => $data['status_pajak'],
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
                    Textarea::make('status_pajak')->placeholder('Dapat dikosongkan tidak perlu diisi'),
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
                        'status_pajak' => $data['status_pajak'],
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
                    $set('status_pajak', $data->status_pajak  ?? '');
                }),
            TextInput::make('nama_pelanggan')->readOnly()->placeholder('Otomatis dari sistem'),
            TextInput::make('alamat_pelanggan')->readOnly()->placeholder('Otomatis dari sistem'),
            TextInput::make('no_telp_perusahaan')->readOnly()->placeholder('Otomatis dari sistem'),
            TextInput::make('email_perusahaan')->readOnly()->placeholder('Otomatis dari sistem'),
            TextInput::make('npwp_perusahaan')->readOnly()->placeholder('Otomatis dari sistem'),
            TextInput::make('no_kontrak_perusahaan')->readOnly()->placeholder('Otomatis dari sistem'),
            TextInput::make('status_pajak')->readOnly()->placeholder('Otomatis dari sistem'),
            TextInput::make('no_group')->required(),
            TextInput::make('no_sertifikat')->required(),
            DatePicker::make('tanggal_sertifikat')->required()->format('d/m/Y')->default(now()),
            DatePicker::make('tanggal_pengiriman_sertifikat')->required()->format('d/m/Y')->default(now()),
            DatePicker::make('tanggal_penerbitan_invoice')->required()->format('d/m/Y')->default(now()),
            DatePicker::make('tanggal_pengiriman_invoice')->required()->format('d/m/Y')->default(now()),
            DatePicker::make('tanggal_pembayaran')->required()->format('d/m/Y')->default(now()),
            DatePicker::make('tanggal_kontrak')->required()->format('d/m/Y')->default(now()),
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
                                                    InvoiceHelper::updateTotals($get, $set);
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
                                                    InvoiceHelper::updateTotals($get, $set);
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
                        ->afterStateUpdated(function (Get $get, Set $set) {
                            InvoiceHelper::updateTotalharga($get, $set);
                        })
                ])
                ->afterStateUpdated(function (Get $get, Set $set) {
                    InvoiceHelper::updateTotalharga($get, $set);
                })
                ->live(debounce: 500)
                ->columnSpanFull(),
            TextInput::make('pembayaran')->required()
                ->live(debounce: 300)
                ->columnSpanFull()
                ->placeholder('Harap diisi untuk mengupdate total harga')
                ->afterStateUpdated(function (Get $get, Set $set) {
                    InvoiceHelper::updateTotalharga($get, $set);
                    if ($get('totalharga_disc') > 5000000) {
                        Notification::make()
                            ->title('E-materai diperlukan')
                            ->body('Total harga melebihi 5 juta, harap unggah e-materai')
                            ->danger()
                            ->send();
                    }
                }),
            Section::make('Kalkulasi Harga')
                ->description('Diskon, PPN, dan Total Harga')
                ->schema([
                    TextInput::make('subtotal')->label('Sub total')->readOnly()->placeholder('Otomatis')->columnSpanFull(),
                    TextInput::make('discount_percentage')
                        ->label('Diskon Percentage')
                        ->suffix('%')
                        ->numeric()
                        ->required()
                        ->minValue(0)
                        ->default(0)
                        ->placeholder('Enter diskon (0-100%)')
                        ->live(debounce: 500)
                        ->afterStateUpdated(function (Get $get, Set $set) {
                            InvoiceHelper::updateTotalharga($get, $set);
                        }),
                    TextInput::make('discon')->label('Diskon')->readOnly()->placeholder('Otomatis')->required(),
                    TextInput::make('ppn_percentage')
                        ->label('PPN Percentage')
                        ->numeric()
                        ->required()
                        ->minValue(0)
                        ->default(11)
                        ->suffix('%')
                        ->placeholder('Enter PPN  (0-100%)')
                        ->live(debounce: 500)
                        ->afterStateUpdated(function (Get $get, Set $set) {
                            InvoiceHelper::updateTotalharga($get, $set);
                        }),
                    TextInput::make('ppn')
                        ->label('PPN')
                        ->numeric()
                        ->required()
                        ->readOnly()
                        ->placeholder('Otomatis')
                        ->minValue(0),

                    TextInput::make('totalharga_disc')->label('Total Harga + Diskon')->readOnly()->placeholder('Otomatis'),
                    TextInput::make('totalharga_ppn_disc')->label('Total Harga + ppn')->readOnly()->placeholder('Otomatis'),

                ])
                ->columns(2)
                ->disabled(fn(Get $get) => $get('pembayaran') == null),
            FileUpload::make('e_materai')
                ->image()
                ->imageEditor()
                ->required(fn(Get $get) => $get('e_matare_status') ? false : true)
                ->columnSpanFull()
                ->hidden(fn(Get $get) => ($get('totalharga_disc') > 5000000) ? false : true),
            Toggle::make('e_matare_status')
                ->label('Tambahkan E-materai nanti')
                ->live(debounce: 500)
                ->hidden(fn(Get $get) => ($get('totalharga_disc') > 5000000) ? false : true),

        ];
    }
}

if (!function_exists('can_edit_invoice')) {
    function can_edit_invoice()
    {
        // dd(auth()->user()->id_departement);
        if (auth()->user()->id_departement != 45) {
            return false;
        }
        return true;
    }
}
// $integer = 6;
// $key = 'yoursecretkey123'; // 16 characters key for AES-128
// $iv = '1234567891011121';  // Initialization vector (16 bytes for AES-128)

// // Encrypt the integer
// $encrypted = encryptInt($integer, $key, $iv);
// echo "Encrypted: " . $encrypted . "\n";

// // Decrypt it back to the original integer
// $decrypted = decryptInt($encrypted, $key, $iv);
// echo "Decrypted: " . $decrypted . "\n";