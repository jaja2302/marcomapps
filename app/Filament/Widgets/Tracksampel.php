<?php

namespace App\Filament\Widgets;

use App\Models\Invoice;
use App\Models\Pengguna;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use App\Models\Tracksampel as TracksampelModel;
use Carbon\Carbon;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Contracts\HasForms;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Tables\Filters\SelectFilter;
use Filament\Forms\Components\DatePicker;
use Filament\Tables\Filters\Filter;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Actions\EditAction;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\Select;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Forms\Components\TextInput;
use Illuminate\Support\Facades\DB;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Mail;
use Filament\Tables\Actions\Action;
use GuzzleHttp\Client;

class Tracksampel extends BaseWidget implements HasForms, HasTable
{
    use InteractsWithTable;
    use InteractsWithForms;
    public function table(Table $table): Table
    {
        return $table
            ->query(TracksampelModel::query()->where('asal_sampel', 'Eksternal'))
            ->columns([
                TextColumn::make('tanggal_terima')
                    ->formatStateUsing(function (TracksampelModel $track) {
                        return tanggal_indo($track->tanggal_terima, false, false, true);
                    })
                    ->toggleable(isToggledHiddenByDefault: false)
                    ->sortable()
                    ->size('xs'),
                TextColumn::make('jenisSampel.nama')
                    ->toggleable(isToggledHiddenByDefault: false)
                    ->label('Jenis Sampel')
                    ->searchable()
                    ->sortable()
                    ->size('xs'),
                TextColumn::make('kode_track')
                    ->toggleable(isToggledHiddenByDefault: false)
                    ->searchable()
                    ->sortable()
                    ->copyable()
                    ->badge()
                    ->color('gray')
                    ->copyMessage(fn(string $state): string => "Copied {$state} to clipboard")
                    ->copyMessageDuration(1500)
                    ->size('xs'),
                TextColumn::make('progressSampel.nama')
                    ->toggleable(isToggledHiddenByDefault: false)
                    ->searchable()
                    ->copyable()
                    ->sortable()
                    ->size('xs'),
                TextColumn::make('nomor_kupa')
                    ->toggleable(isToggledHiddenByDefault: false)
                    ->searchable()
                    ->sortable()
                    ->size('xs'),
                TextColumn::make('nomor_lab')
                    ->toggleable(isToggledHiddenByDefault: false)
                    ->state(function (TracksampelModel $record) {

                        $nolab = explode('$', $record->nomor_lab);
                        $year = Carbon::parse($record->tanggal_terima)->format('y');
                        $kode_sampel = $record->jenisSampel->kode;

                        $nolab = explode('$', $record->nomor_lab);
                        $year = Carbon::parse($record->tanggal_terima)->format('y');
                        $kode_sampel = $record->jenisSampel->kode;

                        $labkiri = $year . $kode_sampel . '.' . formatLabNumber($nolab[0]);
                        $labkanan = isset($nolab[1]) ? $year . $kode_sampel . '.' . formatLabNumber($nolab[1]) : '??';

                        return $labkiri . '-' . $labkanan;
                    })
                    ->searchable()
                    ->sortable()
                    ->size('xs'),
                TextColumn::make('nama_pengirim')
                    ->toggleable(isToggledHiddenByDefault: true)

                    ->searchable()
                    ->sortable()
                    ->size('xs'),

                TextColumn::make('nomor_surat')
                    ->toggleable(isToggledHiddenByDefault: false)

                    ->searchable()
                    ->sortable()
                    ->size('xs'),
                TextColumn::make('departemen')
                    ->toggleable(isToggledHiddenByDefault: false)

                    ->searchable()
                    ->sortable()
                    ->size('xs'),
                TextColumn::make('status')
                    ->badge()
                    ->formatStateUsing(function (TracksampelModel $track) {
                        if ($track->status_changed_by_id != null) {

                            $user = Pengguna::find($track->status_changed_by_id);
                            // dd($track);
                            if ($user && $track->status !== 'Waiting Head Approval') {
                                $roles = $user->getRoleNames();
                                // dd($roles);
                                return $track->status . ' by ' . ($roles->isNotEmpty() ? $roles->implode(', ') : 'No Role');
                            } else {
                                return $track->status;
                            }
                        } else {
                            return $track->status;
                        }
                    })
                    ->toggleable(isToggledHiddenByDefault: false)
                    ->color(function (TracksampelModel $track) {
                        $result = '';
                        switch ($track->status) {
                            case 'Approved':
                                $result = 'success';
                                break;
                            case 'Waiting Admin Approval':
                                $result = 'gray';
                                break;
                            case 'Waiting Head Approval':
                                $result = 'info';
                                break;
                            case 'Rejected':
                                $result = 'danger';
                                break;
                            case 'Draft':
                                $result = 'warning';
                                break;
                            default:
                                $result = 'gray';
                        }
                        return $result;
                    })
                    ->searchable()
                    ->sortable()
                    ->size('xs'),
                TextColumn::make('estimasi')
                    ->formatStateUsing(function (TracksampelModel $track) {
                        return tanggal_indo($track->estimasi, false, false, true);
                    })
                    ->toggleable(isToggledHiddenByDefault: false)
                    // ->datetime()
                    ->searchable()
                    ->sortable()
                    ->size('xs'),
                TextColumn::make('tujuan')
                    ->toggleable(isToggledHiddenByDefault: true)

                    ->searchable()
                    ->sortable()
                    ->size('xs'),
                TextColumn::make('skala_prioritas')
                    ->toggleable(isToggledHiddenByDefault: false)
                    ->searchable()
                    ->badge()
                    ->color(function (TracksampelModel $track) {
                        return $track->skala_prioritas === 'Normal' ? 'gray' : ($track->skala_prioritas === 'Tinggi' ? 'danger' : 'gray');
                    })

                    ->sortable()
                    ->size('xs'),
                TextColumn::make('asal_sampel')
                    ->toggleable(isToggledHiddenByDefault: true)

                    ->searchable()
                    ->sortable()

                    ->size('xs'),
                TextColumn::make('admin')
                    ->toggleable(isToggledHiddenByDefault: true)



                    ->size('xs'),
                TextColumn::make('no_hp')
                    ->toggleable(isToggledHiddenByDefault: true)

                    ->searchable()
                    ->sortable()
                    ->placeholder('-')
                    ->size('xs'),
                TextColumn::make('email')
                    ->toggleable(isToggledHiddenByDefault: true)

                    ->size('xs'),
                TextColumn::make('invoice')
                    ->label('Detail Invoice')
                    ->toggleable(isToggledHiddenByDefault: false)
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'Update' => 'warning',
                        'Invoice Send Pending' => 'warning',
                        'Invoice Send' => 'success',
                        'Default' => 'grey',
                        default => 'danger',
                    })
                    ->state(function (TracksampelModel $record) {
                        if ($record->asal_sampel == 'Eksternal') {
                            if ($record->invoice_status == 0) {
                                return  'Update';
                            } else if ($record->invoice_status == 1) {
                                return  'Invoice Send Pending';
                            } else if ($record->invoice_status == 2) {
                                return  'Invoice Send';
                            } else {
                                return 'Invalid Status';
                            }
                        } else {
                            return 'Default';
                        }
                    })
                    ->size('xs'),
            ])
            ->recordClasses(fn(TracksampelModel $record) => match ($record->progressSampel->nama) {
                'Recheck' => 'bg-yellow-100',
                'Rilis Sertifikat' => 'bg-green-100',
                default => null,
            })
            ->filters([
                SelectFilter::make('jenisSampel')
                    ->label('Jenis sampel')
                    ->relationship('jenisSampel', 'nama')
                    // ->multiple()
                    ->preload(),
                SelectFilter::make('skala_prioritas')
                    ->options([
                        'normal' => 'Normal',
                        'tinggi' => 'Tinggi',
                    ])
                    ->indicateUsing(function (array $data): ?string {
                        if (!$data['value']) {
                            return null;
                        }
                        return  'Skala Prioritas : ' . ($data['value'] === 'normal' ? 'Normal' : 'Tinggi');
                    }),
                Filter::make('created_at')
                    ->form([
                        DatePicker::make('created_from')
                            ->label('Tanggal terima dari'),
                        DatePicker::make('created_until')
                            ->label('Tanggal terima sampai'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        // dd($data);
                        return $query
                            ->when(
                                $data['created_from'],
                                function (Builder $query, $date) {
                                    // dd($query->whereDate('tanggal_terima', '>=', $date));

                                    return $query->whereDate('tanggal_terima', '>=', $date);
                                }
                                // fn (Builder $query, $date): Builder => $query->whereDate('tanggal_terima', '>=', $date),
                            )
                            ->when(
                                $data['created_until'],
                                function (Builder $query, $date) {
                                    return $query->whereDate('tanggal_terima', '<=', $date);
                                }
                            );
                    })

            ])
            ->actions([
                EditAction::make('edit_invoice')
                    ->label(fn(TracksampelModel $record): string => $record->invoice_status == '0' ? 'Edit' : 'Uptodate')
                    ->icon('heroicon-o-document-arrow-down')
                    ->color('success')
                    ->disabled(
                        fn(TracksampelModel $record) =>
                        $record->asal_sampel == 'Internal' ? true : ($record->invoice_status == '0' ? false : true)
                    )
                    // ->hidden(fn(TracksampelModel $record): string => $record->asal_sampel == 'Internal')
                    // ->visible(fn($record) => auth()->user()->can('send_invoice') && $record->asal_sampel === 'Eksternal')
                    ->modalHeading(fn(TracksampelModel $record) => "Edit Invoice " . $record->kode_track)
                    ->modalSubmitActionLabel('Submit')
                    ->form([
                        Fieldset::make('Detail')
                            ->label('Pastikan mengisi detail pelanggan dengan benar')
                            ->schema([
                                Select::make('list')
                                    ->label('List Detail Pelanggan')
                                    ->columnSpanFull()
                                    ->searchable()
                                    ->live(debounce: 500)
                                    ->required()
                                    ->afterStateUpdated(function ($state, Set $set) {
                                        // dd($state);
                                        if ($state !== '0') {
                                            $invoice = Invoice::find($state);
                                            // dd($invoice, $state);
                                            $set('nama_pelanggan', $invoice->nama);
                                            $set('alamat_pelanggan', $invoice->address);
                                            $set('npwp', $invoice->npwp_id);
                                        } else {
                                            $set('nama_pelanggan', '');
                                            $set('alamat_pelanggan', '');
                                            $set('npwp', '');
                                        }
                                    })
                                    ->options(function () {
                                        $data = Invoice::query()->pluck('nama', 'id')->toArray();
                                        $default = [0 => 'Tidak Ada'];
                                        return $default + $data;
                                    }),
                                TextInput::make('nama_pelanggan')
                                    ->label('Nama Pelanggan')
                                    ->readOnly(fn(Get $get) => $get('list') !== '0' ? true : false)
                                    ->maxLength(100)
                                    ->placeholder('Silahkan pilih detail pelanggan mengisi manual')
                                    ->required(),
                                TextInput::make('alamat_pelanggan')
                                    ->label('Alamat Pelanggan')
                                    ->maxLength(200)
                                    ->placeholder('Silahkan pilih detail pelanggan mengisi manual')
                                    ->readOnly(fn(Get $get) => $get('list') !== '0' ? true : false)
                                    ->required(),
                                TextInput::make('npwp')
                                    ->label('NPWP')
                                    ->placeholder('Silahkan pilih detail pelanggan mengisi manual')
                                    ->readOnly(fn(Get $get) => $get('list') !== '0' ? true : false)
                                    ->maxLength(200)
                                    ->required(),
                            ])
                            ->columns(3),
                        Fieldset::make('invoice')
                            ->label('Detail Invoice')
                            ->schema([
                                DatePicker::make('tanggal_invoice')
                                    ->label('Tanggal Invoice')
                                    ->required(),
                                TextInput::make('no_kontrak')
                                    ->label('No. Kontrak')
                                    ->maxLength(200),
                                DatePicker::make('tanggal_kontrak')
                                    ->label('Tanggal Kontrak'),
                                TextInput::make('pembayaran')
                                    ->label('Pembayaran')
                                    ->maxLength(200)
                                    ->required(),
                                TextInput::make('statuss')
                                    ->label('Status Pajak / Kurs')
                                    ->maxLength(200)
                                    ->required(),
                            ])
                            ->columns(3)
                    ])
                    ->successNotification(null)
                    ->using(function (TracksampelModel $record, array $data): TracksampelModel {

                        // dd($data);
                        // dd($statusdata);
                        try {
                            DB::beginTransaction();
                            $invoice = new Invoice();
                            $invoice->nama = $data['nama_pelanggan'];
                            $invoice->address = $data['alamat_pelanggan'];
                            $invoice->npwp_id = $data['npwp'];
                            $invoice->created_by = auth()->user()->id;
                            $invoice->save();
                            $id_data = $invoice->id;

                            $record->invoice_id = $id_data;
                            $record->no_kontrak = $data['no_kontrak'];
                            $record->tanggal_kontrak = $data['tanggal_kontrak'];
                            $record->tanggal_invoice = $data['tanggal_invoice'];
                            $record->pembayaran = $data['pembayaran'];
                            $record->status_pajak = $data['statuss'];
                            $record->invoice_status = 1;
                            $record->save();

                            DB::commit();
                            Notification::make()
                                ->success()
                                ->title('Verifikasi Berhasil')
                                ->body('Invoice berhasil di verifikasi')
                                ->send();
                        } catch (\Throwable $th) {
                            DB::rollBack();

                            Notification::make()
                                ->title('Error ' . $th->getMessage())
                                ->danger()
                                ->color('danger')
                                ->send();
                        }
                        return $record;
                    }),
                Action::make('send_invoice')
                    ->label(fn(TracksampelModel $record): string => $record->invoice_status !== '2' ? 'Send' : 'Sended')
                    ->action(function (TracksampelModel $records) {
                        // dd($records);
                        $nomor_hp = $records->no_hp;
                        $nomor_hp = explode(',', $nomor_hp);
                        $dataToInsert2 = [];
                        foreach ($nomor_hp as $data) {
                            $nomor_hp = numberformat_excel($data);

                            if ($nomor_hp !== 'Error') {
                                $dataToInsert2[] = [
                                    'no_surat' => $records->nomor_surat,
                                    'nama_departemen' => $records->departemen,
                                    'jenis_sampel' => $records->jenisSampel->nama,
                                    'jumlah_sampel' => $records->jumlah_sampel,
                                    'progresss' => $records->progressSampel->nama,
                                    'kodesample' => $records->kode_track,
                                    'penerima' =>  str_replace('+', '', $data),
                                    'type' => 'input',
                                    'asal' => $records->asal_sampel,
                                    'id_invoice' => $records->id,
                                ];
                            }
                        }
                        $emailAddresses = !empty($records->emailTo) ? explode(',', $records->emailTo) : null;
                        $emailcc = !empty($records->emailCc) ? explode(',', $records->emailCc) : null;
                        // dd($emailAddresses, $emailcc);
                        // dd($dataToInsert2);
                        if (!empty($dataToInsert2)) {
                            // dd($dataToInsert2);
                            // event(new Smartlabsnotification($dataToInsert2));
                            // SendMsg::insert($dataToInsert2);
                        }

                        // dd($progress, $progress_state);
                        if ($emailAddresses !== null) {
                            // Mail::to($emailAddresses)
                            //     ->cc($emailcc)
                            //     ->send(new EmailPelanggan($records->nomor_surat, $records->departemen, $records->jenisSampel->nama, $records->jumlah_sampel, $records->progressSampel->nama, $records->kode_track, $records->id));
                        }
                        $records->invoice_status = 2;
                        $records->save();
                        return Notification::make()
                            ->success()
                            ->title('Invoice Berhasil Dikirim')
                            ->body('Invoice berhasil dikirim ke pelanggan')
                            ->send();
                    })
                    ->icon('heroicon-o-document-chart-bar')
                    ->disabled(function (TracksampelModel $record) {
                        if ($record->asal_sampel === 'Eksternal') {

                            if ($record->invoice_status == 1) {
                                return false;
                            } else {
                                return true;
                            }
                        } else {
                            return true;
                        }
                    })
                    ->openUrlInNewTab()
                    ->color('success')
                    // ->visible(fn($record) => auth()->user()->can('send_invoice') && $record->asal_sampel === 'Eksternal')
                    ->size('xs'),
                Action::make('download_invoice')
                    ->label('Download')
                    ->action(function (TracksampelModel $record) {
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
                                $pdfFilename = 'invoice_' .
                                    str_replace(['/', '\\'], '_', $record->jenisSampel->nama) . '_' .
                                    str_replace(['/', '\\'], '_', $record->nomor_surat) . '.pdf';

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
                    // ->visible(fn($record) => auth()->user()->can('send_invoice') && $record->asal_sampel === 'Eksternal')
                    ->size('xs')

            ]);;
    }
}
