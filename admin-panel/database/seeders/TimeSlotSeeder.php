<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use App\Models\BackendGuy;
use App\Models\TimeSlot;

class TimeSlotSeeder extends Seeder
{
    public function run()
    {
    //     $backendGuys = BackendGuy::all(); // Fetch all backend guys

    //     $startDate = Carbon::today();
    //     $endDate = Carbon::today()->addDays(7); // Next 7 days

    //     foreach ($backendGuys as $backendGuy) {
    //         $date = $startDate;
    //         while ($date <= $endDate) {
    //             $startTime = Carbon::createFromTime(9, 0, 0);
    //             $endTime = Carbon::createFromTime(18, 0, 0);

    //             while ($startTime < $endTime) {
    //                 TimeSlot::create([
    //                     'backend_guy_id' => $backendGuy->id,
    //                     'date' => $date->format('Y-m-d'),
    //                     'start_time' => $startTime->format('H:i:s'),
    //                     'is_available' => true,
    //                 ]);
                    
    //                 $startTime->addMinutes(15); // Increment by 15 mins
    //             }
    //             $date->addDay();
    //         }
    //     }
    }
}
