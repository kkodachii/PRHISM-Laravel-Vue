<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EquipmentsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        $equipments = [
            ['equipment_name' => 'ECG Machine', 'batch_id' => 2, 'description' => 'Electrocardiogram machine', 'quantity' => 5, 'provider' => 'Provider X', 'date_acquired' => now(), 'user_id' => 1, 'status' => 'Good condition', 'accountable' => 'kean lucas', 'barangay_id' => rand(1, 3)],
            ['equipment_name' => 'Defibrillator', 'batch_id' => 2, 'description' => 'Defibrillator machine', 'quantity' => 3, 'provider' => 'Provider Y', 'date_acquired' => now(), 'user_id' => 1, 'status' => 'Good condition', 'accountable' => 'kean lucas', 'barangay_id' => rand(1, 3)],
            ['equipment_name' => 'X-ray Machine', 'batch_id' => 2, 'description' => 'X-ray machine for radiology', 'quantity' => 2, 'provider' => 'Provider Z', 'date_acquired' => now(), 'user_id' => 1, 'status' => 'Poor condition', 'accountable' => 'kean lucas', 'barangay_id' => rand(1, 3)],
            ['equipment_name' => 'Ultrasound Machine', 'batch_id' => 2, 'description' => 'Ultrasound diagnostic machine', 'quantity' => 4, 'provider' => 'Provider A', 'date_acquired' => now(), 'user_id' => 1, 'status' => 'Good condition', 'accountable' => 'kean lucas', 'barangay_id' => rand(1, 3)],
            ['equipment_name' => 'MRI Machine', 'batch_id' => 2, 'description' => 'Magnetic Resonance Imaging machine', 'quantity' => 1, 'provider' => 'Provider B', 'date_acquired' => now(), 'user_id' => 1, 'status' => 'Poor condition', 'accountable' => 'kean lucas', 'barangay_id' => rand(1, 3)],
            ['equipment_name' => 'CT Scanner', 'batch_id' => 2, 'description' => 'Computed Tomography scanner', 'quantity' => 2, 'provider' => 'Provider C', 'date_acquired' => now(), 'user_id' => 1, 'status' => 'Poor condition', 'accountable' => 'kean lucas', 'barangay_id' => rand(1, 3)],
            ['equipment_name' => 'Ventilator', 'batch_id' => 2, 'description' => 'Ventilator for respiratory support', 'quantity' => 6, 'provider' => 'Provider D', 'date_acquired' => now(), 'user_id' => 1, 'status' => 'Good condition', 'accountable' => 'kean lucas', 'barangay_id' => rand(1, 3)],
            ['equipment_name' => 'Infusion Pump', 'batch_id' => 2, 'description' => 'Pump for IV administration', 'quantity' => 10, 'provider' => 'Provider E', 'date_acquired' => now(), 'user_id' => 1, 'status' => 'Good condition', 'accountable' => 'kean lucas', 'barangay_id' => rand(1, 3)],
            ['equipment_name' => 'Anesthesia Machine', 'batch_id' => 2, 'description' => 'Anesthesia delivery machine', 'quantity' => 3, 'provider' => 'Provider F', 'date_acquired' => now(), 'user_id' => 1, 'status' => 'Poor condition', 'accountable' => 'kean lucas', 'barangay_id' => rand(1, 3)],
            ['equipment_name' => 'Surgical Lights', 'batch_id' => 2, 'description' => 'Lights for surgery', 'quantity' => 5, 'provider' => 'Provider G', 'date_acquired' => now(), 'user_id' => 1, 'status' => 'Good condition', 'accountable' => 'kean lucas', 'barangay_id' => rand(1, 3)],
            ['equipment_name' => 'Operating Table', 'batch_id' => 2, 'description' => 'Table for surgery', 'quantity' => 3, 'provider' => 'Provider H', 'date_acquired' => now(), 'user_id' => 1, 'status' => 'Poor condition', 'accountable' => 'kean lucas', 'barangay_id' => rand(1, 3)],
            ['equipment_name' => 'Patient Monitor', 'batch_id' => 2, 'description' => 'Monitor for patient vitals', 'quantity' => 7, 'provider' => 'Provider I', 'date_acquired' => now(), 'user_id' => 1, 'status' => 'Good condition', 'accountable' => 'kean lucas', 'barangay_id' => rand(1, 3)],
            ['equipment_name' => 'Suction Apparatus', 'batch_id' => 2, 'description' => 'Suction device for medical use', 'quantity' => 8, 'provider' => 'Provider J', 'date_acquired' => now(), 'user_id' => 1, 'status' => 'Good condition', 'accountable' => 'kean lucas', 'barangay_id' => rand(1, 3)],
            ['equipment_name' => 'Autoclave', 'batch_id' => 2, 'description' => 'Sterilization device', 'quantity' => 4, 'provider' => 'Provider K', 'date_acquired' => now(), 'user_id' => 1, 'status' => 'Good condition', 'accountable' => 'kean lucas', 'barangay_id' => rand(1, 3)],
            ['equipment_name' => 'Pulse Oximeter', 'batch_id' => 2, 'description' => 'Device to measure oxygen saturation', 'quantity' => 15, 'provider' => 'Provider L', 'date_acquired' => now(), 'user_id' => 1, 'status' => 'Good condition', 'accountable' => 'kean lucas', 'barangay_id' => rand(1, 3)],
            ['equipment_name' => 'Blood Pressure Monitor', 'batch_id' => 2, 'description' => 'Device to measure blood pressure', 'quantity' => 10, 'provider' => 'Provider M', 'date_acquired' => now(), 'user_id' => 1, 'status' => 'Good condition', 'accountable' => 'kean lucas', 'barangay_id' => rand(1, 3)],
            ['equipment_name' => 'Nebulizer', 'batch_id' => 2, 'description' => 'Device for respiratory treatments', 'quantity' => 12, 'provider' => 'Provider N', 'date_acquired' => now(), 'user_id' => 1, 'status' => 'Good condition', 'accountable' => 'kean lucas', 'barangay_id' => rand(1, 3)],
            ['equipment_name' => 'Glucometer', 'batch_id' => 2, 'description' => 'Device to measure blood glucose', 'quantity' => 20, 'provider' => 'Provider O', 'date_acquired' => now(), 'user_id' => 1, 'status' => 'Good condition', 'accountable' => 'kean lucas', 'barangay_id' => rand(1, 3)],
            ['equipment_name' => 'Thermometer', 'batch_id' => 2, 'description' => 'Device to measure body temperature', 'quantity' => 25, 'provider' => 'Provider P', 'date_acquired' => now(), 'user_id' => 1, 'status' => 'Good condition', 'accountable' => 'kean lucas', 'barangay_id' => rand(1, 3)],
            ['equipment_name' => 'ECG Electrodes', 'batch_id' => 2, 'description' => 'Electrodes for ECG machine', 'quantity' => 100, 'provider' => 'Provider Q', 'date_acquired' => now(), 'user_id' => 1, 'status' => 'Good condition', 'accountable' => 'kean lucas', 'barangay_id' => rand(1, 3)],
        ];


        DB::table('equipments')->insert($equipments);
    }
}
