<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProgressPengerjaan extends Model
{
    use HasFactory;
    protected $connection = 'mysql3';
    protected $table = 'progress_pengerjaan';
    protected $guarded = ['id'];
}
