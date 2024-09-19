<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    use HasFactory;
    protected $connection = 'mysql3';
    protected $table = 'eksternal_detail';
    public $timestamps = false;
    protected $guarded = ['id'];
    public function Invoice()
    {
        return $this->hasMany(TrackSampel::class, 'id', 'invoice_id');
    }
}
