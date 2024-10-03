<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TrackParameter extends Model
{
    use HasFactory;
    // protected $connection = 'mysql3';
    protected $table = 'track_parameter';
    protected $guarded = ['id'];
    public $timestamps = false;

    public function ParameterAnalisis()
    {
        return $this->belongsTo(ParameterAnalisis::class, 'id_parameter');
    }
}
