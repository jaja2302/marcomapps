<?php

namespace App\Filament\Widgets;

use App\Models\Databaseinvoice;
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
}
