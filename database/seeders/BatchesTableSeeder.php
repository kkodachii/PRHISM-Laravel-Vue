<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BatchesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('batches')->insert([
            ['batch_number' => '11-03-2024_10-35-07', 'barangay_id' => 1],
            ['batch_number' => '11-15-2024_09-10-10', 'barangay_id' => 1],
            ['batch_number' => '12-03-2024_01-24-50', 'barangay_id' => 1],
            ['batch_number' => '11-03-2024_10-36-57', 'barangay_id' => 2],
            ['batch_number' => '11-15-2024_09-26-10', 'barangay_id' => 2],
            ['batch_number' => '12-03-2024_01-24-50', 'barangay_id' => 2],
            ['batch_number' => '11-03-2024_10-37-40', 'barangay_id' => 3],
            ['batch_number' => '11-15-2024_09-30-10', 'barangay_id' => 3],
            ['batch_number' => '12-03-2024_01-24-50', 'barangay_id' => 3],
        ]);
    }
}

