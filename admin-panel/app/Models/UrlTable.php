<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UrlTable extends Model
{
    use HasFactory;

    protected $table = 'url_table';

    protected $fillable = ['assessment_request_id', 'url', 'channel','token'];

    public function assessmentRequest()
    {
        return $this->belongsTo(AssessmentRequest::class, 'assessment_request_id');
    }
}
