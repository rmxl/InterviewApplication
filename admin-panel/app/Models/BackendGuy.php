<?php
namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;

class BackendGuy extends Authenticatable {
    protected $fillable = ['username', 'password'];
    protected $hidden = ['password'];

    public function timeSlots(){
        return $this->hasMany(TimeSlot::class);
    }
}
