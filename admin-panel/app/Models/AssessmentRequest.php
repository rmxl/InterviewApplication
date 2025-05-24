<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AssessmentRequest extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'backend_guy_id', 'time_slot_id', 'assessment_type'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function backendGuy()
    {
        return $this->belongsTo(BackendGuy::class);
    }

    public function timeSlot()
    {
        return $this->belongsTo(TimeSlot::class);
    }
}
