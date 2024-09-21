<?php

namespace App\Filament\Resources\DatabaseResource\Pages;

use App\Filament\Resources\DatabaseResource;
use App\Models\Databaseinvoice;
use App\Models\Detailresi;
use Carbon\Carbon;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;

class CreateDatabase extends CreateRecord
{
    protected static string $resource = DatabaseResource::class;

    protected function handleRecordCreation(array $data): Model
    {
        return DB::transaction(function () use ($data) {
            // Create new Databaseinvoice instance
            $query = Databaseinvoice::create([
                'perusahaan_id' => $data['nama_perusahaan'],
                'nama_pelanggan' => $data['nama_pelanggan'],
                'no_group' => $data['no_group'],
                'tanggal_sertifikat' => $data['tanggal_sertifikat'],
                'tanggal_penerbitan_invoice' => $data['tanggal_penerbitan_invoice'],
                'tujuan_pengiriman' => $data['tujuan_pengiriman'],
                'status_pembayaran' => $data['pembayaran'],
                'tanggal_pembayaran' => $data['tanggal_pembayaran'],
                'resi_pengiriman' => $this->generateResiPengiriman(),
            ]);

            // Create and save detail resi
            Detailresi::create([
                'resi_id' => $query->resi_pengiriman,
                'data' => json_encode($data['letterDetails']),
            ]);
            return $query; // Return the created model
        });
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
