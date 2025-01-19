<?php

namespace Database\Seeders;

use Carbon\Carbon;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $barangays = [
            ['barangay_name' => 'RHU main', 'rhu_id' => 1],
            ['barangay_name' => 'RHU 2', 'rhu_id' => 2],
            ['barangay_name' => 'RHU 3', 'rhu_id' => 3],
            ['barangay_name' => 'Binakod', 'rhu_id' => 1],
            ['barangay_name' => 'Kapitangan', 'rhu_id' => 2],
            ['barangay_name' => 'Malumot', 'rhu_id' => 1],
            ['barangay_name' => 'Masukol', 'rhu_id' => 1],
            ['barangay_name' => 'Pinalagdan', 'rhu_id' => 2],
            ['barangay_name' => 'Poblacion', 'rhu_id' => 1],
            ['barangay_name' => 'San Isidro I', 'rhu_id' => 2],
            ['barangay_name' => 'San Isidro II', 'rhu_id' => 2],
            ['barangay_name' => 'San Jose', 'rhu_id' => 1],
            ['barangay_name' => 'San Roque', 'rhu_id' => 1],
            ['barangay_name' => 'San Vicente', 'rhu_id' => 1],
            ['barangay_name' => 'Santa Cruz', 'rhu_id' => 1],
            ['barangay_name' => 'Santo Niño', 'rhu_id' => 2],
            ['barangay_name' => 'Santo Rosario', 'rhu_id' => 1],
        ];

        $users = [
            // Existing admin and super-admin users
            [
                'name' => 'Jericho Nomat',
                'email' => 'jericho2@gmail.com',
                'email_verified_at' => now(),
                'password' => Hash::make('password123'),
                'remember_token' => Str::random(10),
                'role_id' => 2, // Admin
                'rhu_id' => 2,
                'barangay_id' => 2,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Jericho Nomat',
                'email' => 'jericho@gmail.com',
                'email_verified_at' => now(),
                'password' => Hash::make('password123'),
                'remember_token' => Str::random(10),
                'role_id' => 2, // Admin
                'rhu_id' => 1,
                'barangay_id' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Kean Lucas',
                'email' => 'kean2@gmail.com',
                'email_verified_at' => now(),
                'password' => Hash::make('password123'),
                'remember_token' => Str::random(10),
                'role_id' => 3, // Super-admin
                'rhu_id' => 1,
                'barangay_id' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Kean Lucas',
                'email' => 'kean@gmail.com',
                'email_verified_at' => now(),
                'password' => Hash::make('password123'),
                'remember_token' => Str::random(10),
                'role_id' => 3, // Super-admin
                'rhu_id' => 1,
                'barangay_id' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        // Add midwife accounts based on barangay names
        foreach ($barangays as $index => $barangay) {
            // Skip barangay_id 1, 2, and 3
            if (in_array($index + 1, [1, 2, 3])) {
                continue;
            }

            $users[] = [
                'name' => $barangay['barangay_name'], // Set name as barangay_name
                'email' => strtolower(str_replace(' ', '', $barangay['barangay_name'])) . '@gmail.com', // Remove spaces from email
                'email_verified_at' => now(),
                'password' => Hash::make('password123'), // Secure password
                'remember_token' => Str::random(10),
                'role_id' => 1, // Assuming 1 is the role ID for midwife
                'rhu_id' => $barangay['rhu_id'],
                'barangay_id' => $index + 1, // Assuming barangay IDs are sequential starting from 1
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }


        // Insert into the database
        DB::table('users')->insert($users);

    }
}

