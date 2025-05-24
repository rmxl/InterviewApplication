<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Job;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder {
    public function run(): void {
        $job = Job::where('jobType', 'tiffin_master')->first();
        
        User::firstOrCreate(
            ['id' => 1], // Ensure the user with ID 1 exists
            [
                'name' => 'John Doe',
                'username' => 'johndoe',
                'password' => Hash::make('password123'),
                'jobType_Id' => 1,
                'experience_level' => 'Intermediate',
            ]
        );
    }
}
