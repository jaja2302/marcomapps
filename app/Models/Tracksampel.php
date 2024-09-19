<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tracksampel extends Model
{
    use HasFactory;
    protected $connection = 'mysql3';

    protected $table = 'track_sampel';
    public $timestamps = false;
    protected $guarded = ['id'];
    protected $casts = [
        'foto_sampel' => 'array',
    ];
    public function jenisSampel()
    {
        return $this->belongsTo(JenisSampel::class, 'jenis_sampel', 'id');
    }

    public function progressSampel()
    {
        return $this->belongsTo(ProgressPengerjaan::class, 'progress');
    }


    public function trackParameters()
    {
        return $this->hasMany(TrackParameter::class, 'id_tracksampel', 'parameter_analisisid');
    }
    public function Invoice()
    {
        return $this->belongsTo(Invoice::class, 'invoice_id', 'id');
    }
}
