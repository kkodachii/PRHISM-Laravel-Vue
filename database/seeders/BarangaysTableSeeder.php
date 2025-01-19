<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class BarangaysTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
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

        DB::table('barangays')->insert($barangays);
    }
}
