<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Question extends Model {
    use HasFactory;

    protected $fillable = ['text', 'body', 'job_type', 'experience_level']; // Fields that can be mass assigned
}
