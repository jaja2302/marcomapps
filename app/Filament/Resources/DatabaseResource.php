<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DatabaseResource\Pages;
use App\Models\Databaseinvoice;
use App\Models\Detailresi;
use App\Models\Perusahaan;
use Carbon\Carbon;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Table;
use Filament\Pages\SubNavigationPosition;
use Filament\Notifications\Notification;
use Filament\Tables\Actions\Action;
use GuzzleHttp\Client;
use Filament\Tables\Columns\TextColumn;
use Filament\Resources\Pages\Page;
use Illuminate\Database\Eloquent\Model;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Support\Facades\DB;
use Filament\Forms\Components\DatePicker;
use Filament\Tables\Filters\Filter;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Enums\FiltersLayout;

class DatabaseResource extends Resource
{
    protected static ?string $model = Databaseinvoice::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static SubNavigationPosition $subNavigationPosition = SubNavigationPosition::End;
    protected static ?string $modelLabel = 'invoice';
    // protected static ?string $recordTitleAttribute = 'Perusahaan.nama';

    public static function getGloballySearchableAttributes(): array
    {
        return ['Perusahaan.nama', 'Perusahaan.nama_pelanggan', 'no_group'];
    }
    protected static ?string $navigationLabel = 'invoice';
    protected static ?string $navigationGroup = 'Dashboard';
    public $e_materai_status = true;

    public static function form(Form $form): Form
    {
        return $form
            ->schema(form_invoice());
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
                    ->searchable()
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
                    ->searchable()
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
                IconColumn::make('e_materai_status')
                    ->sortable()
                    ->color(fn(string $state): string => match ($state) {
                        'Tidak Memerlukan E-materai' => 'info',
                        'Harap Upload E-materai' => 'warning',
                        'E-materai sudah diupload' => 'success',
                        default => 'gray',
                    })
                    ->icon(fn(string $state): string => match ($state) {
                        'Harap Upload E-materai' => 'heroicon-o-cloud-arrow-up',
                        'Tidak Memerlukan E-materai' => 'heroicon-o-x-mark',
                        'E-materai sudah diupload' => 'heroicon-o-check-circle',
                        'Invalid Status' => 'heroicon-o-check-circle',
                    })
                    ->state(function (Databaseinvoice $record) {
                        if ($record->e_materai_status == 1 && $record->e_materai == null) {
                            return 'Harap Upload E-materai';
                        } elseif ($record->e_materai_status == 0 && $record->e_materai == null) {
                            return 'Tidak Memerlukan E-materai';
                        } elseif ($record->e_materai_status == 0 && $record->e_materai !== null) {
                            return 'E-materai sudah diupload';
                        } else {
                            return 'Invalid Status';
                        }
                    })
                    ->size(IconColumn\IconColumnSize::Medium),
            ])
            ->bulkActions([
                DeleteBulkAction::make()->visible(can_edit_invoice()),
            ])
            ->filters([
                SelectFilter::make('nama_perusahaan')
                    ->relationship('Perusahaan', 'nama')
                    ->options(Perusahaan::pluck('nama', 'id')->toArray()),
                Filter::make('tanggal_penerbitan_invoice')
                    ->form([
                        DatePicker::make('created_from')->label('Tanggal dari Penerbitan invoice'),
                        DatePicker::make('created_until')->label('Sampai Tanggal Penerbitan invoice'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['created_from'],
                                fn(Builder $query, $date): Builder => $query->whereDate('tanggal_penerbitan_invoice', '>=', $date),
                            )
                            ->when(
                                $data['created_until'],
                                fn(Builder $query, $date): Builder => $query->whereDate('tanggal_penerbitan_invoice', '<=', $date),
                            );
                    })
            ], layout: FiltersLayout::Modal)
            ->actions([
                Action::make('download_invoice')
                    ->label('Download Invoice')
                    ->action(function (Databaseinvoice $record) {
                        $client = new Client();
                        // dd($record);
                        if ($record->id !== null) {
                            // Make a GET request to the API with query parameters
                            $response = $client->get('http://erpda.test/api/invoices_smartlabs', [
                                'query' => [
                                    'email' => 'j',
                                    'password' => 'j',
                                    'id_data' => $record->id,
                                ],
                            ]);
                            // $response = $client->get('https://management.srs-ssms.com/api/invoices_smartlabs', [
                            //     'query' => [
                            //         'email' => 'j',
                            //         'password' => 'j',
                            //         'id_data' => $record->id,
                            //     ],
                            // ]);

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
                    ->size('xs'),
                ActionGroup::make([
                    EditAction::make()
                        ->mutateRecordDataUsing(function (array $data): array {
                            // dd($data);
                            $perusahaan = Perusahaan::where('id', $data['perusahaan_id'])->first();
                            $resi = Detailresi::where('resi_id', $data['resi_pengiriman'])->first();
                            $resi_data = json_decode($resi->data, true);
                            // dd($resi_data);
                            $data['nama_perusahaan'] = $data['perusahaan_id'];
                            $data['nama_pelanggan'] = $perusahaan->nama;
                            $data['status_pajak'] = $perusahaan->status_pajak;
                            $data['alamat_pelanggan'] = $perusahaan->alamat_pelanggan;
                            $data['no_telp_perusahaan'] = $perusahaan->no_telp_perusahaan;
                            $data['email_perusahaan'] = $perusahaan->email_perusahaan;
                            $data['npwp_perusahaan'] = $perusahaan->npwp_perusahaan;
                            $data['no_kontrak_perusahaan'] = $perusahaan->no_kontrak_perusahaan;
                            $data['letterDetails'] = $resi_data;
                            $data['pembayaran'] = $data['status_pembayaran'];
                            $data['discount_percentage'] = $resi->discount;
                            $data['totalharga'] = $resi->total_harga;
                            $data['totalharga_disc'] = $resi->totalharga_disc;

                            return $data;
                        })
                        ->using(function (Model $record, array $data): Model {
                            return DB::transaction(function () use ($data, $record) {
                                // Update Databaseinvoice instance
                                Databaseinvoice::where('id', $record->id)->update([
                                    'perusahaan_id' => $data['nama_perusahaan'] ?? null,
                                    'no_group' => $data['no_group'] ?? null,
                                    'tanggal_sertifikat' => Carbon::createFromFormat('d/m/Y', $data['tanggal_sertifikat'])->format('Y-m-d H:i:s'),
                                    'tanggal_pengiriman_sertifikat' => Carbon::createFromFormat('d/m/Y', $data['tanggal_pengiriman_sertifikat'])->format('Y-m-d H:i:s'),
                                    'no_sertifikat' => $data['no_sertifikat'],
                                    'tanggal_penerbitan_invoice' => Carbon::createFromFormat('d/m/Y', $data['tanggal_penerbitan_invoice'])->format('Y-m-d H:i:s'),
                                    'tanggal_pengiriman_invoice' => Carbon::createFromFormat('d/m/Y', $data['tanggal_pengiriman_invoice'])->format('Y-m-d H:i:s'),
                                    'status_pembayaran' => $data['pembayaran'],
                                    'tanggal_kontrak' => Carbon::createFromFormat('d/m/Y', $data['tanggal_kontrak'])->format('Y-m-d H:i:s'),
                                    'tanggal_pembayaran' => Carbon::createFromFormat('d/m/Y', $data['tanggal_pembayaran'])->format('Y-m-d H:i:s'),
                                    'faktur_pajak' => $data['faktur_pajak'] ?? null,
                                    'e_materai' => $data['e_materai'] ?? null,
                                    'e_materai_status' => isset($data['e_materai_status']) ? ($data['e_materai_status'] ? 1 : 0) : 0,
                                    'created_by' => auth()->user()->user_id,
                                ]);

                                // Update Detailresi related data
                                Detailresi::where('resi_id', $record->resi_pengiriman)->update([
                                    'data' => json_encode($data['letterDetails'] ?? []),
                                    'discount' => $data['discount_percentage'] ?? 0,
                                    'total_harga' => $data['totalharga'] ?? 0,
                                    'totalharga_disc' => $data['totalharga_disc'] ?? 0,
                                ]);

                                return $record; // Optionally return the updated record
                            });
                        })
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title('Invoice Updated')
                                ->body('Perubahan invoice berhasil disimpan'),
                        )
                        ->form(form_invoice()),
                    Action::make('delete')
                        ->action(function (Databaseinvoice $record) {
                            $record->delete();
                            Detailresi::where('resi_id', $record->resi_pengiriman)->delete();
                            Notification::make()
                                ->title("Berhasil di Hapus")
                                ->success()
                                ->send();
                        })
                        ->visible(can_edit_invoice())
                        ->deselectRecordsAfterCompletion()
                        ->icon('heroicon-o-trash')
                        ->color('danger')
                        ->requiresConfirmation()
                        ->modalHeading('Delete invoices')
                        ->modalSubheading(
                            "Anda yakin ingin menghapus invoice ini? Ketika dihapus tidak dapat dipulihkan kembali."
                        )
                        ->modalButton('Yes'),
                ]),
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
