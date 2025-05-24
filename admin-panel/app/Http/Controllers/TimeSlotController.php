<?php

namespace App\Http\Controllers;

use App\Models\TimeSlot;
use App\Models\User;
use Illuminate\Http\Request;
use App\Models\BackendGuy;  
use App\Models\AssessmentRequest;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;


class TimeSlotController extends Controller
{
    public function index()
    {
        // Get all available time slots, order by date and start time, and return a unique list of start times
        $timeSlots = TimeSlot::where('is_available', true)
            ->orderBy('date')
            ->orderBy('start_time')
            ->get()
            ->unique('start_time')
            ->values();

        return response()->json($timeSlots);
    }

    /**
     * Get available time slots for the given date and the next date.
     * Expects a 'date' parameter in YYYY-MM-DD format.
     */
    public function getAvailableSlots(Request $request)
    {
        $request->validate([
            'date' => 'required|date',
        ]);

        // Parse the provided date and compute the next date
        $date = Carbon::parse($request->date)->format('Y-m-d');
        $nextDate = Carbon::parse($date)->addDay()->format('Y-m-d');

        $availableSlots = TimeSlot::where('is_available', true)
            ->where(function ($query) use ($date, $nextDate) {
                $query->where('date', $date)
                    ->orWhere('date', $nextDate);
            })
            ->orderBy('date')
            ->orderBy('start_time')
            ->get();

        return response()->json(['available_slots' => $availableSlots], 200);
    }

    /**
     * Reschedule a user to a new time slot.
     * 1. Find the user's assessment request in the assessment_requests table and delete it.
     * 2. Mark the old time slot (associated with the previous assessment) as available.
     * 3. Create a new assessment request for the new time slot and mark that slot as unavailable.
     *
     * Expects 'user_id' and 'new_time_slot_id' in the request.
     */
    public function reschedule(Request $request)
    {
        $request->validate([
            'username' => 'required|exists:users,username',
            'date' => 'required',
            'start_time' => 'required',
        ]);

        DB::beginTransaction();

        $user = User::where('username', $request->username)->first();

        if (!$user) {
//            DB::rollBack();
            return response()->json(['message' => 'User not found'], 400);
        }

        try {
            // 1. Find the current assessment for the given user.
            $existingAssessments = AssessmentRequest::where('user_id', $user->id)->get();

            if ($existingAssessments->isEmpty()) {
                DB::rollBack();
                return response()->json(['message' => 'No assessment request found for the user'], 404);
            }

            // Log how many assessments are found
//            \Log::info('Assessments found:', ['count' => $existingAssessments->count()]);

            // Delete all assessments for the user
            foreach ($existingAssessments as $assessment) {
                // 2. Get the old time slot and mark it as available.
                $oldTimeSlot = TimeSlot::find($assessment->time_slot_id);
                if ($oldTimeSlot) {
                    $oldTimeSlot->is_available = true;
                    $oldTimeSlot->save();
                }
                $assessment->delete();
            }

            // 3. Get the new time slot and ensure it's available.
            $newTimeSlot = TimeSlot::where('date', $request->date)
                ->where('start_time', $request->start_time)
                ->first();

            if ($newTimeSlot == null) {
                DB::rollBack();
                return response()->json(['message' => 'New time slot is not available'], 400);
            }

            if (!$newTimeSlot->is_available) {
                DB::rollBack();
                return response()->json(['message' => 'New time slot is not available'], 400);
            }

            // Create a new assessment request using the new time slot.
            $newAssessment = AssessmentRequest::create([
                'user_id' => $user->id,
                'backend_guy_id' => $newTimeSlot->backend_guy_id,
                'time_slot_id' => $newTimeSlot->id,
                'assessment_type' => 'scheduled'
            ]);

            // Mark the new time slot as not available.
            $newTimeSlot->is_available = false;
            $newTimeSlot->save();

            DB::commit();
            return response()->json([
                'message' => 'Rescheduled successfully',
                'assessment' => $newAssessment
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Reschedule failed',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function getTimeSlotsForDate($date, Request $request)
    {
        $request->validate([
            'backend_guy_id' => 'required|exists:backend_guys,id',
        ]);

        $timeSlots = TimeSlot::where('backend_guy_id', $request->backend_guy_id)
            ->where('date', $date)
            ->get();

        return response()->json($timeSlots, 200); // Removed 'time_slots' key
    }


    public function getTimeSlotDetails($id)
    {
        $timeSlot = TimeSlot::find($id);

        if (!$timeSlot) {
            return response()->json(['message' => 'Time slot not found'], 404);
        }

        return response()->json($timeSlot, 200);
    }

    public function addTimeSlot(Request $request)
    {
        $request->validate([
            'backend_guy_id' => 'required|exists:backend_guys,id',
            'date' => 'required|date',
            'start_time' => 'required',
            'is_available' => 'required|boolean',
        ]);

        $timeSlot = TimeSlot::create($request->all());
        return response()->json(['message' => 'Time slot created successfully', 'time_slot' => $timeSlot], 201);
    }


    public function updateTimeSlot($id, Request $request)
    {
        $request->validate([
            'backend_guy_id' => 'required|exists:backend_guys,id',
            'start_time' => 'required',
            'end_time' => 'nullable',
            'is_available' => 'required|boolean',
        ]);

        $timeSlot = TimeSlot::find($id);

        if (!$timeSlot) {
            return response()->json(['message' => 'Time slot not found'], 404);
        }

        $timeSlot->update($request->all());

        return response()->json(['message' => 'Time slot updated successfully', 'time_slot' => $timeSlot], 200);
    }


    public function deleteTimeSlot($id)
    {
        $timeSlot = TimeSlot::find($id);

        if (!$timeSlot) {
            return response()->json(['message' => 'Time slot not found'], 404);
        }

        $timeSlot->delete();

        return response()->json(['message' => 'Time slot deleted successfully'], 200);
    }

    public function generateTimeSlots($backendGuyId)
    {
        $backendGuy = BackendGuy::findOrFail($backendGuyId);
        
        // Get the last date from time slots
        $lastTimeSlot = TimeSlot::where('backend_guy_id', $backendGuyId)
            ->orderBy('date', 'desc')
            ->first();

        if (!$lastTimeSlot) {
            // No time slots exist, create for next 7 days
            $this->createTimeSlots($backendGuy, Carbon::today(), Carbon::today()->addDays(7));
            return redirect()->back()->with('success', 'Initial time slots generated successfully!');
        }

        $lastDate = Carbon::parse($lastTimeSlot->date);
        $twoDaysBeforeLastDate = $lastDate->copy()->subDays(2);

        if (Carbon::today()->lte($twoDaysBeforeLastDate)) {
            return redirect()->back()->with('info', 'Time slots are already generated for upcoming days.');
        }

        // Generate time slots from the day after last date to next 7 days
        $startDate = $lastDate->copy()->addDay();
        $endDate = Carbon::today()->addDays(7);
        
        $this->createTimeSlots($backendGuy, $startDate, $endDate);
        
        return redirect()->back()->with('success', 'Time slots extended successfully!');
    }

    private function createTimeSlots($backendGuy, $startDate, $endDate)
    {
        $date = $startDate;
        while ($date <= $endDate) {
            $startTime = Carbon::createFromTime(9, 0, 0);
            $endTime = Carbon::createFromTime(18, 0, 0);

            while ($startTime < $endTime) {
                TimeSlot::create([
                    'backend_guy_id' => $backendGuy->id,
                    'date' => $date->format('Y-m-d'),
                    'start_time' => $startTime->format('H:i:s'),
                    'is_available' => true,
                ]);
                
                $startTime->addMinutes(15);
            }
            $date->addDay();
        }
    }

}
