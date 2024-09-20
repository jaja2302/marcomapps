<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JenisAnalisa extends Model
{
    use HasFactory;
    protected $table = 'jenis_analisa';
    public $timestamps = false;
    protected $guarded = ['id'];

    public function Databaseinvoice()
    {
        return $this->hasMany(Databaseinvoice::class, 'id', 'jenis_analisa_id');
    }
}
