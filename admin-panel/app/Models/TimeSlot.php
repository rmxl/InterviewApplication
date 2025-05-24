<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TimeSlot extends Model
{
    use HasFactory;

    protected $fillable = ['backend_guy_id', 'date', 'start_time', 'end_time', 'is_available'];

    public function backendGuy()
    {
        return $this->belongsTo(BackendGuy::class);
    }
}