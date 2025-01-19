<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MedicalSuppliesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        $medicalSupplies = [
            ['med_sup_name' => 'Gauze Pads', 'batch_id' => 3, 'description' => 'Sterile gauze pads', 'quantity' => 100, 'provider' => 'Provider A', 'date_acquired' => now(), 'user_id' => 1, 'barangay_id' => rand(1, 3)],
            ['med_sup_name' => 'Bandages', 'batch_id' => 3, 'description' => 'Elastic bandages', 'quantity' => 150, 'provider' => 'Provider B', 'date_acquired' => now(), 'user_id' => 1, 'barangay_id' => rand(1, 3)],
            ['med_sup_name' => 'Syringes', 'batch_id' => 3, 'description' => 'Disposable syringes', 'quantity' => 200, 'provider' => 'Provider C', 'date_acquired' => now(), 'user_id' => 1, 'barangay_id' => rand(1, 3)],
            ['med_sup_name' => 'Gloves', 'batch_id' => 3, 'description' => 'Medical examination gloves', 'quantity' => 300, 'provider' => 'Provider D', 'date_acquired' => now(), 'user_id' => 1, 'barangay_id' => rand(1, 3)],
            ['med_sup_name' => 'Face Masks', 'batch_id' => 3, 'description' => 'Disposable face masks', 'quantity' => 400, 'provider' => 'Provider E', 'date_acquired' => now(), 'user_id' => 1, 'barangay_id' => rand(1, 3)],
            ['med_sup_name' => 'Alcohol Swabs', 'batch_id' => 3, 'description' => 'Sterile alcohol swabs', 'quantity' => 500, 'provider' => 'Provider F', 'date_acquired' => now(), 'user_id' => 1, 'barangay_id' => rand(1, 3)],
            ['med_sup_name' => 'IV Sets', 'batch_id' => 3, 'description' => 'Intravenous sets', 'quantity' => 60, 'provider' => 'Provider G', 'date_acquired' => now(), 'user_id' => 1, 'barangay_id' => rand(1, 3)],
            ['med_sup_name' => 'Thermometers', 'batch_id' => 3, 'description' => 'Digital thermometers', 'quantity' => 70, 'provider' => 'Provider H', 'date_acquired' => now(), 'user_id' => 1, 'barangay_id' => rand(1, 3)],
            ['med_sup_name' => 'Scalpel Blades', 'batch_id' => 3, 'description' => 'Sterile scalpel blades', 'quantity' => 80, 'provider' => 'Provider I', 'date_acquired' => now(), 'user_id' => 1, 'barangay_id' => rand(1, 3)],
            ['med_sup_name' => 'Surgical Tapes', 'batch_id' => 3, 'description' => 'Medical adhesive tapes', 'quantity' => 90, 'provider' => 'Provider J', 'date_acquired' => now(), 'user_id' => 1, 'barangay_id' => rand(1, 3)],
            ['med_sup_name' => 'Cotton Balls', 'batch_id' => 3, 'description' => 'Sterile cotton balls', 'quantity' => 110, 'provider' => 'Provider K', 'date_acquired' => now(), 'user_id' => 1, 'barangay_id' => rand(1, 3)],
            ['med_sup_name' => 'Tweezers', 'batch_id' => 3, 'description' => 'Medical tweezers', 'quantity' => 40, 'provider' => 'Provider L', 'date_acquired' => now(), 'user_id' => 1, 'barangay_id' => rand(1, 3)],
            ['med_sup_name' => 'Tongue Depressors', 'batch_id' => 3, 'description' => 'Wooden tongue depressors', 'quantity' => 140, 'provider' => 'Provider M', 'date_acquired' => now(), 'user_id' => 1, 'barangay_id' => rand(1, 3)],
            ['med_sup_name' => 'Sterilization Wraps', 'batch_id' => 3, 'description' => 'Wraps for sterilization', 'quantity' => 120, 'provider' => 'Provider N', 'date_acquired' => now(), 'user_id' => 1, 'barangay_id' => rand(1, 3)],
            ['med_sup_name' => 'Surgical Caps', 'batch_id' => 3, 'description' => 'Disposable surgical caps', 'quantity' => 130, 'provider' => 'Provider O', 'date_acquired' => now(), 'user_id' => 1, 'barangay_id' => rand(1, 3)],
            ['med_sup_name' => 'Surgical Gowns', 'batch_id' => 3, 'description' => 'Disposable surgical gowns', 'quantity' => 160, 'provider' => 'Provider P', 'date_acquired' => now(), 'user_id' => 1, 'barangay_id' => rand(1, 3)],
            ['med_sup_name' => 'Eye Shields', 'batch_id' => 3, 'description' => 'Protective eye shields', 'quantity' => 170, 'provider' => 'Provider Q', 'date_acquired' => now(), 'user_id' => 1, 'barangay_id' => rand(1, 3)],
            ['med_sup_name' => 'Stethoscopes', 'batch_id' => 3, 'description' => 'Medical stethoscopes', 'quantity' => 50, 'provider' => 'Provider R', 'date_acquired' => now(), 'user_id' => 1, 'barangay_id' => rand(1, 3)],
            ['med_sup_name' => 'Blood Collection Tubes', 'batch_id' => 3, 'description' => 'Sterile blood collection tubes', 'quantity' => 60, 'provider' => 'Provider S', 'date_acquired' => now(), 'user_id' => 1, 'barangay_id' => rand(1, 3)],
            ['med_sup_name' => 'Suture Kits', 'batch_id' => 3, 'description' => 'Sterile suture kits', 'quantity' => 80, 'provider' => 'Provider T', 'date_acquired' => now(), 'user_id' => 1, 'barangay_id' => rand(1, 3)],
        ];


        foreach ($medicalSupplies as &$supply) {
            $supply['status'] = $this->determineStatus($supply['quantity']);
        }

        DB::table('medical_supplies')->insert($medicalSupplies);
    }

    private function determineStatus($quantity)
    {
        if ($quantity === 0) {
            return 'Out of stock';
        } elseif ($quantity <= 20) {
            return 'Low on stock';
        } elseif ($quantity >= 21) {
            return 'On stock';
        }
        return ''; // Default case if needed
    }
}
