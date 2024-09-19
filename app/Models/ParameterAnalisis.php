<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ParameterAnalisis extends Model
{
    use HasFactory;
    protected $connection = 'mysql2';
    protected $table = 'parameter_analisis';
    protected $guarded = ['id'];
    public $timestamps = false;
    public function jenisSampel()
    {
        return $this->belongsTo(JenisSampel::class, 'id_jenis_sampel');
    }
}
