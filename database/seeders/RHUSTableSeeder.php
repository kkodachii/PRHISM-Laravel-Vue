<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RhusTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('rhus')->insert([
            ['rhu_name' => 'RHU Main','location'=> 'paombong'],
            ['rhu_name' => 'RHU 2','location'=> 'paombong'],
            ['rhu_name' => 'RHU 3','location'=> 'paombong'],
            // Add other roles as needed
        ]);
    }
}
