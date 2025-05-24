<?php

namespace Database\Seeders;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Seeder;
use App\Models\Job;

class JobSeeder extends Seeder
{
    public function run(): void
    {
        // Clear the jobs table

        $jobs = [
            ['jobType' => 'tiffin_master'],
            ['jobType' => 'tiffin_assistant'],
            ['jobType' => 'tea_master'],
            ['jobType' => 'coffee_master'],
            ['jobType' => 'tea_coffee_maker'],
            ['jobType' => 'tea_stall_helper'],
            ['jobType' => 'juice_smoothie_maker'],
            ['jobType' => 'chapati_rice_maker'],
            ['jobType' => 'ice_cream_counter'],
            ['jobType' => 'bakery_assistant'],
            ['jobType' => 'home_chef'],
        ];

        foreach ($jobs as $job) {
            Job::create($job);
        }
    }
}
