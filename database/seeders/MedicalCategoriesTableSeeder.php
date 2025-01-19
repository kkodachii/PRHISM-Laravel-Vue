<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MedicalCategoriesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        DB::table('medical_categories')->insert([
            ['category' => 'Maintenance'],
            ['category' => 'Insulin and Other Diabetic'],
            ['category' => 'Immunization'],
            ['category' => 'Family Planning'],
            ['category' => 'Tuberculosis'],
            ['category' => 'Micronutrients and Supplements'],
            ['category' => 'IMCI'],
            ['category' => 'Infectious Disease'],
            ['category' => 'Mental Health'],
            ['category' => 'Pain Management and Anti-inflammatory'],
            ['category' => 'Cardiovascular'],
            ['category' => 'Respiratory'],
            ['category' => 'Gastrointestinal'],
            ['category' => 'Hormonal and Endocrine'],
            ['category' => 'Neurological'],
            ['category' => 'Dermatological'],
            ['category' => 'Oncology'],
            ['category' => 'Oral Health'],
            ['category' => 'Eye and Ear'],
            ['category' => 'Reproductive Health'],
            ['category' => 'Blood Disorders'],
            ['category' => 'Emergency and Critical Care'],
        ]);
    }
}
