<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Pengguna extends Authenticatable
{
    use HasFactory, Notifiable;
    protected $connection = 'mysql2';
    protected $table = 'pengguna';
    protected $primaryKey = 'user_id';
    public $timestamps = false;
    protected $fillable = ['email'];
    public function notifications()
    {
        return $this->hasMany(Notification::class, 'notifiable_id', 'user_id');
    }
}
