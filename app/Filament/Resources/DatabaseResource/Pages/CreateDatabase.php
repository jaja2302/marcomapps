<?php

namespace App\Filament\Resources\DatabaseResource\Pages;

use App\Filament\Resources\DatabaseResource;
use App\Models\Databaseinvoice;
use App\Models\Detailresi;
use Carbon\Carbon;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;
use Filament\Notifications\Actions\Action;
use Filament\Forms\Components\FileUpload;

class CreateDatabase extends CreateRecord
{
    protected static string $resource = DatabaseResource::class;

    protected function handleRecordCreation(array $data): Model
    {
        // dd($data);
        // dd($total_harga_letterDetails);
        return DB::transaction(function () use ($data) {
            // Create new Databaseinvoice instance


            // dd($data);
            $query = Databaseinvoice::create([
                'perusahaan_id' => $data['nama_perusahaan'],
                'no_group' => $data['no_group'],
                'tanggal_sertifikat' => Carbon::createFromFormat('d/m/Y', $data['tanggal_sertifikat'])->format('Y-m-d H:i:s'),
                'tanggal_pengiriman_sertifikat' => Carbon::createFromFormat('d/m/Y', $data['tanggal_pengiriman_sertifikat'])->format('Y-m-d H:i:s'),
                'no_sertifikat' => $data['no_sertifikat'],
                'tanggal_penerbitan_invoice' => Carbon::createFromFormat('d/m/Y', $data['tanggal_penerbitan_invoice'])->format('Y-m-d H:i:s'),
                'tanggal_pengiriman_invoice' => Carbon::createFromFormat('d/m/Y', $data['tanggal_pengiriman_invoice'])->format('Y-m-d H:i:s'),
                'status_pembayaran' => $data['pembayaran'],
                'tanggal_pembayaran' => Carbon::createFromFormat('d/m/Y', $data['tanggal_pembayaran'])->format('Y-m-d H:i:s'),
                'faktur_pajak' => $data['faktur_pajak'],
                'e_materai' => isset($data['e_materai']) ? $data['e_materai'] : null,
                'e_materai_status' => isset($data['e_matare_status']) ? ($data['e_matare_status'] ? 1 : 0) : 0,
                'resi_pengiriman' => $this->generateResiPengiriman(),
                'created_by' => auth()->user()->user_id,
            ]);

            // Create and save detail resi
            Detailresi::create([
                'resi_id' => $query->resi_pengiriman,
                'data' => json_encode($data['letterDetails']),
                'discount' => $data['discount_percentage'],
                'total_harga' => $data['totalharga'],
                'totalharga_disc' => $data['totalharga_disc'],
            ]);
            return $query; // Return the created model
        });
    }

    protected function beforeCreate(): void
    {
        // dd(auth()->user()->id_jabatan);
        if (!can_edit_invoice()) {
            Notification::make()
                ->warning()
                ->title('Akses Ditolak')
                ->body('Anda tidak memiliki akses untuk mengakses halaman ini')
                ->persistent()
                ->send();

            $this->halt();
        }
    }

    private function generateResiPengiriman(): string
    {
        $randomString = Str::upper(Str::random(10)); // Random 10 character string
        $randomNumber = rand(1000, 9999); // 4-digit random number
        return Carbon::now()->format('Ymd') . $randomString . $randomNumber;
    }

    protected function getCreatedNotificationTitle(): ?string
    {
        return 'Data invoice berhasil ditambahkan';
    }
}
