<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Databaseinvoice extends Model
{
    use HasFactory;
    protected $table = 'database_invoice';
    public $timestamps = false;
    protected $guarded = ['id'];

    public function Perusahaan()
    {
        return $this->belongsTo(Perusahaan::class, 'perusahaan_id', 'id');
    }

    public function Residetail()
    {
        return $this->belongsTo(Detailresi::class, 'resi_pengiriman', 'resi_id');
    }
}
