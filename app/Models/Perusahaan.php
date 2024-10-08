<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Perusahaan extends Model
{
    use HasFactory;

    protected $table = 'perusahaan';
    public $timestamps = false;
    protected $guarded = ['id'];
    public function Databaseinvoice()
    {
        return $this->hasMany(Databaseinvoice::class, 'id', 'perusahaan_id');
    }
}
