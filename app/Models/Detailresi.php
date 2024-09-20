<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Detailresi extends Model
{
    use HasFactory;
    protected $table = 'detail_resi';
    public $timestamps = false;
    protected $guarded = ['id'];

    public function Databaseinvoice()
    {
        return $this->hasMany(Databaseinvoice::class, 'resi_id', 'resi_pengiriman');
    }
}
