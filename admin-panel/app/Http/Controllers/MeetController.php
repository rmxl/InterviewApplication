<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\QuestionBank;
use App\Models\UrlTable;
use App\Models\User;
use App\Models\AssessmentRequest;

class MeetController extends Controller
{
    protected $questionBank;

    public function __construct(QuestionBank $questionBank)
    {
        $this->questionBank = $questionBank;
    }

    public function index(Request $request, $url)
    {
        if(!session('backendGuy')) {
            return redirect()->route('login');
        }

        $meetingData = UrlTable::where('url', $url)->first();
        
        if (!$meetingData) {
            return redirect()->route('dashboard')->with('error', 'Invalid meeting URL');
        }
        
        $assessment_request_id = $request->input('assessment_request_id');
        $assessmentRequest = AssessmentRequest::find($assessment_request_id);
        if (!$assessmentRequest) {
            return redirect()->route('dashboard')->with('error', 'Invalid assessment request ID');
        }
        $user = User::find($assessmentRequest->user_id);
        if (!$user) {
            return redirect()->route('dashboard')->with('error', 'Invalid user ID');
        }
        $job_id = $user->jobType_Id; 
        $experienceLevel = $user->experience_level; 
        $questions = $this->questionBank->fetchQuestions($job_id, $experienceLevel);

        return view('meet', [
            'channel' => $meetingData->channel,
            'token' => $meetingData->token,
            'questions' => $questions,
            'assessment_request_id' => $meetingData->assessment_request_id
        ]);
    }

    public function joinMeeting($url)
    {
        $meetingData = UrlTable::where('url', $url)->first();
        
        if (!$meetingData) {
            return redirect()->route('dashboard')->with('error', 'Invalid meeting URL');
        }
        
        return view('usermeet', [
            'channel' => $meetingData->channel,
            'token' => $meetingData->token,
        ]);
    } 
}