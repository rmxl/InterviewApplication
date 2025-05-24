<?php

namespace App\Http\Controllers;

use App\Models\AssessmentRequest;
use App\Models\TimeSlot;
use App\Models\User;
use Illuminate\Http\Request;

class AssessmentController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'username' => 'required|exists:users,username',
        ]);


        $user = User::where('username', $request->username)->first();

        if (!$user) {
            return response()->json(['message' => 'User not found'], 400);
        }

        $timeSlot = null;


        if ($request->assessment_type == 'scheduled') {
            $timeSlot = TimeSlot::where('date', $request->date)
                ->where('start_time', $request->start_time)
                ->first();

            if (!$timeSlot) {
                return response()->json(['message' => 'Time Slot not found'], 400);
            }

            if (!$timeSlot->is_available) {
                return response()->json(['message' => 'Time slot is not available'], 400);
            }
        }

        //check if user already has an assessment
        $existingAssessment = AssessmentRequest::where('user_id', $user->id)
            ->where('is_done', false)
            ->first();
        if ($existingAssessment) {
            return response()->json(['message' => 'User already has a pending assessment'], 400);
        }


        // Create the assessment

        try {
            $assessment = AssessmentRequest::create([
                'assessment_type' => $request->assessment_type,
                'user_id' => $user->id,
                'time_slot_id' => $timeSlot ? $timeSlot->id : null,
                'backend_guy_id' => $timeSlot ? $timeSlot->backend_guy_id : null,
            ]);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error creating assessment: ' . $e->getMessage()], 500);
        }

        if ($timeSlot) {
            $timeSlot->is_available = false;
            $timeSlot->save();
        }

        return response()->json([
            'message' => 'Assessment created successfully',
            'assessment' => $assessment,
        ], 201);
    }

    public function getAssessmentsForMonth(Request $request)
    {
        $request->validate([
            'start' => 'required|date',
            'end' => 'required|date',
            'backend_guy_id' => 'required|exists:backend_guys,id',
        ]);

        $assessments = AssessmentRequest::whereHas('timeSlot', function ($query) use ($request) {
            $query->where('backend_guy_id', $request->backend_guy_id)
                ->whereBetween('date', [$request->start, $request->end])
                ->where('is_done', false);
        })
            ->with(['user', 'timeSlot']) // This ensures timeSlot is loaded with each assessment
            ->get();

        return response()->json($assessments);
    }


    public function getAssessmentsForDate($date, Request $request)
    {
        $request->validate([
            'backend_guy_id' => 'required|exists:backend_guys,id',
        ]);

        $assessments = AssessmentRequest::whereHas('timeSlot', function ($query) use ($request, $date) {
            $query->where('backend_guy_id', $request->backend_guy_id)
                ->where('date', $date)
                ->where('is_done', false);
        })
            ->with(['user', 'timeSlot'])
            ->get();

        return response()->json($assessments, 200); // Removed the 'assessments' key
    }


    public function getPendingAssessments(Request $request)
    {
        $pendingAssessments = AssessmentRequest::whereHas('timeSlot', function ($query) use ($request) {
            $query->where('backend_guy_id', $request->backend_guy_id)
                ->where('is_done', false);
        })
            ->with(['user', 'timeSlot'])
            ->get();

        //fetch the instant assessments also, they have no backend id
        $instantAssessments = AssessmentRequest::where('backend_guy_id', null)
            ->where('is_done', false)
            ->with(['user'])
            ->get();
        $pendingAssessments = $pendingAssessments->merge($instantAssessments);

        return response()->json(['pending_assessments' => $pendingAssessments], 200);
    }

    public function saveRating(Request $request)
    {
        $request->validate([
            'assessment_id' => 'required|exists:assessment_requests,id',
            'rating' => 'required|numeric|min:0|max:10',
            'showed_up' => 'required|boolean',
        ]);

        $assessment = AssessmentRequest::find($request->assessment_id);

        if (!$assessment) {
            return response()->json(['message' => 'Assessment not found'], 404);
        }

        $assessment->rating = $request->rating;
        $assessment->is_done = true;
        $assessment->showed_up = $request->showed_up;
        $assessment->save();

        return response()->json(['message' => 'Rating saved successfully'], 200);
    }

    public function getAssessment($username)
    {
        $user = User::where('username', $username)->first();

        if (!$user) {
            return response()->json(['message' => 'User not found'], 400);
        }


        $assessment = AssessmentRequest::where('user_id', $user->id)
            ->where('is_done', false)
            ->first();

        // Check if assessments exist
        if (!$assessment) {
            $assessment = AssessmentRequest::where('user_id', $user->id)->where('is_done', true)->first();
            if ($assessment) {
                return response()->json([
                    'has_scheduled_meeting' => false,
                    'assessment_type' => $assessment->assessment_type,
                    'id' => $assessment->id,
                    'user_id' => $assessment->user_id,
                    'date' => optional($assessment->timeSlot)->date,  // Ensure no errors if null
                    'start_time' => optional($assessment->timeSlot)->start_time,
                    'is_done' => $assessment->is_done,
                ]);
            } else {
                return response()->json([
                    'has_scheduled_meeting' => false,
                ]);
            }
        }

        return response()->json([
            'has_scheduled_meeting' => true,
            'id' => $assessment->id,
            'user_id' => $assessment->user_id,
            'date' => optional($assessment->timeSlot)->date,  // Ensure no errors if null
            'start_time' => optional($assessment->timeSlot)->start_time,
        ]);
    }

    public function requestInstantAssessment(Request $request)
    {
        $user = User::where('username', $request->username)->first();

        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        // Check if the user already has a pending assessment
        $existingAssessment = AssessmentRequest::where('user_id', $user->id)
            ->where('is_done', false)
            ->first();

        if ($existingAssessment) {
            return response()->json(['message' => 'User already has a pending assessment'], 400);
        }

        //create the assessment
        $assessment = AssessmentRequest::create([
            'user_id' => $user->id,
            'assessment_type' => 'instant',
        ]);

        return response()->json($assessment, 201);
    }

    public function cancelAssessment(Request $request)
    {
        $user = User::where('username', $request->username)->first();

        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        // Check if the user already has a pending assessment
        $assessment = AssessmentRequest::where('user_id', $user->id)
            ->where('is_done', false)
            ->first();

        if (!$assessment) {
            return response()->json(['message' => 'User has a no assessments'], 400);
        }

        $assessment->delete();

        return response()->json(['message' => 'meeting cancelled successfully'], 200);
    }

    public function getAssessmentRequest($assessmentRequestId)
    {
        $assessmentRequest = AssessmentRequest::with(['user', 'timeSlot'])->find($assessmentRequestId);

        if (!$assessmentRequest) {
            return response()->json(['message' => 'Assessment request not found'], 404);
        }
        return response()->json(['user_id' => $assessmentRequest->user_id], 200);
    }
}
