<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder {
    public function run(): void {
        $this->call([
            JobSeeder::class,
            BackendGuySeeder::class,
            QuestionSeeder::class,
            TimeSlotSeeder::class,
            UserSeeder::class,
            AssessmentRequestSeeder::class,
        ]);
    }
}

