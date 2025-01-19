<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Support\Str;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MidwifeInventorySeeder extends Seeder
{
    public function run()
    {
        $userRhuBarangay = [
            ['user_id' => 1, 'rhu_id' => 1, 'barangay_id' => 6],
            ['user_id' => 2, 'rhu_id' => 2, 'barangay_id' => 5],
            ['user_id' => 3, 'rhu_id' => 1, 'barangay_id' => 4],
        ];

        $medicines = [
            'Paracetamol' => [
                'Biogesic 500mg, 20 tablets per pack',
                'Panadol 500mg, 20 tablets per pack',
                'Acetaminophen 500mg, 20 tablets per pack'
            ],
            'Ibuprofen' => [
                'Advil 200mg, 20 tablets per pack',
                'Motrin 200mg, 20 tablets per pack',
                'Nurofen 200mg, 20 tablets per pack'
            ],
            'Amoxicillin' => [
                'Amoxil 500mg, 30 capsules per pack',
                'Trimox 500mg, 30 capsules per pack',
                'Moxatag 775mg, 30 tablets per pack'
            ],
            'Metformin' => [
                'Glucophage 500mg, 30 tablets per pack',
                'Fortamet 500mg, 30 tablets per pack',
                'Glumetza 500mg, 30 tablets per pack'
            ],
            'Amlodipine' => [
                'Norvasc 5mg, 30 tablets per pack',
                'Amlodipine 5mg, 30 tablets per pack',
                'Katerzia 5mg, 30 tablets per pack'
            ],
            'Ciprofloxacin' => [
                'Cipro 500mg, 20 tablets per pack',
                'Cipro XR 500mg, 10 tablets per pack',
                'Ciprofloxacin 500mg, 20 tablets per pack'
            ],
            'Loratadine' => [
                'Claritin 10mg, 30 tablets per pack',
                'Alavert 10mg, 30 tablets per pack',
                'Loratadine 10mg, 30 tablets per pack'
            ],
            'Omeprazole' => [
                'Prilosec 20mg, 30 capsules per pack',
                'Losec 20mg, 30 capsules per pack',
                'Omeprazole 20mg, 30 tablets per pack'
            ],
            'Cetirizine' => [
                'Zyrtec 10mg, 30 tablets per pack',
                'Reactine 10mg, 30 tablets per pack',
                'Cetirizine 10mg, 30 tablets per pack'
            ],
            'Hydrochlorothiazide' => [
                'Hydrodiuril 25mg, 30 tablets per pack',
                'HCTZ 25mg, 30 tablets per pack',
                'Microzide 25mg, 30 tablets per pack'
            ]
        ];

        $equipment = [
            'Stethoscope', 'Blood Pressure Monitor', 'Thermometer', 'Syringe Pump',
            'Oxygen Cylinder', 'Wheelchair', 'Glucometer', 'Nebulizer',
            'Pulse Oximeter', 'Defibrillator',
        ];

        $medicalSupplies = [
            'Surgical Gloves', 'Face Mask', 'Cotton Swabs', 'Gauze Pads',
            'Alcohol Pads', 'IV Cannula', 'Bandages', 'Syringes',
            'Adhesive Tapes', 'Thermal Blankets',
        ];

        for ($i = 0; $i < 10; $i++) {
            $user = $userRhuBarangay[array_rand($userRhuBarangay)];

            // Pick a random medicine and its packaging details
            $medicineName = array_rand($medicines); // Pick the medicine name
            $brandsAndPackaging = $medicines[$medicineName]; // Get the packaging options for that medicine
            $brandAndPackaging = $brandsAndPackaging[array_rand($brandsAndPackaging)]; // Pick a random brand and packaging

            $this->createMedicine($user, $medicineName, $brandAndPackaging);
            $this->createEquipment($user, $equipment[array_rand($equipment)]);
            $this->createMedicalSupply($user, $medicalSupplies[array_rand($medicalSupplies)]);
        }
    }

    private function createMedicine($userRhuBarangay, $medicineName, $brandAndPackaging)
    {
        $quantity = rand(20, 100);
        $expirationDate = Carbon::now()->addMonths(rand(6, 24));
        DB::table('midwife_inventory')->insert([
            'user_id' => $userRhuBarangay['user_id'],
            'rhu_id' => $userRhuBarangay['rhu_id'],
            'barangay_id' => $userRhuBarangay['barangay_id'],
            'type' => 'medicine',
            'name' => $medicineName,
            'quantity' => $quantity,
            'brand' => $brandAndPackaging,
            'category_id' => rand(1, 5),
            'expiration_date' => $expirationDate,
            'status' => $this->computeStatus($quantity, $expirationDate),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    private function createEquipment($userRhuBarangay, $equipmentName)
    {
        $status = $this->computeEquipmentStatus();
        DB::table('midwife_inventory')->insert([
            'user_id' => $userRhuBarangay['user_id'],
            'rhu_id' => $userRhuBarangay['rhu_id'],
            'barangay_id' => $userRhuBarangay['barangay_id'],
            'type' => 'equipment',
            'name' => $equipmentName,
            'quantity' => rand(1, 50),
            'description' => 'This is a description for equipment ' . Str::random(5),
            'status' => $status,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    private function createMedicalSupply($userRhuBarangay, $medicalSupplyName)
    {
        $quantity = rand(5, 200);
        DB::table('midwife_inventory')->insert([
            'user_id' => $userRhuBarangay['user_id'],
            'rhu_id' => $userRhuBarangay['rhu_id'],
            'barangay_id' => $userRhuBarangay['barangay_id'],
            'type' => 'medical_supply',
            'name' => $medicalSupplyName,
            'quantity' => $quantity,
            'description' => 'This is a description for medical supply ' . Str::random(5),
            'status' => $this->computeMedicineStatus($quantity), // Adjust if needed
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    private function computeMedicineStatus($quantity)
    {
        if ($quantity === 0) {
            return "Out of stock";
        } elseif ($quantity <= 20) {
            return "Low on stock";
        } else {
            return "On stock";
        }
    }

    private function computeStatus($quantity, $expirationDate)
    {
        $currentDate = Carbon::now();
        $expiryDate = Carbon::parse($expirationDate);
        $threeMonthsFromNow = $currentDate->copy()->addMonths(3);
        $statuses = [];

        if ($expiryDate < $currentDate) {
            return "Expired";
        }

        if ($quantity === 0) {
            $statuses[] = "Out of stock";
        } elseif ($quantity <= 20) {
            $statuses[] = "Low on stock";
        } else {
            $statuses[] = "On stock";
        }

        if ($expiryDate <= $threeMonthsFromNow) {
            $statuses[] = "Expiring";
        }

        return implode(', ', $statuses);
    }

    private function computeEquipmentStatus()
    {
        $conditions = ["Good condition", "Fair condition", "Poor condition", "New"];
        return $conditions[array_rand($conditions)];
    }
}
