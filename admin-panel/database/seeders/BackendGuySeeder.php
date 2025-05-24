<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\BackendGuy;
use Illuminate\Support\Facades\Hash;

class BackendGuySeeder extends Seeder
{
    public function run(): void
    {
        $backendGuys = [
            [
                'username' => 'admin',
                'password' => Hash::make('password123'),
            ],
            [
                'username' => 'moderator',
                'password' => Hash::make('password123'),
            ],
        ];

        foreach ($backendGuys as $backendGuy) {
            // Use firstOrCreate to prevent duplicate entries
            BackendGuy::firstOrCreate(
                ['username' => $backendGuy['username']],
                ['password' => $backendGuy['password']]
            );
        }
    }
}
