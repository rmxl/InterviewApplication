<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Job extends Model
{
    use HasFactory;

    protected $fillable = [
        'jobType'
    ];

    // Relationship with Users
    public function users()
    {
        return $this->hasMany(User::class, 'jobType_Id');
    }

    // Relationship with Questions
    public function questions()
    {
        return $this->hasMany(Question::class, 'jobType_Id');
    }
}
