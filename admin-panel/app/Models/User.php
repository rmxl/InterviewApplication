<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Relations\HasMany;

class User extends Authenticatable {
    use HasFactory;

    protected $fillable = ['name', 'username', 'password'];

   public function job()
   {
       return $this->belongsTo(Job::class, 'jobType_Id');
   }
}
