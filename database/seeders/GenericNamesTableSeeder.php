<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class GenericNamesTableSeeder extends Seeder
{
    public function run()
    {
        $genericNames = [
            // Maintenance Medicines
            1 => [
                'Amlodipine',
                'Losartan',
                'Metformin',
                'Atorvastatin',
                'Simvastatin'
            ],

            // Insulin and Other Diabetic Medicines
            2 => [
                'Insulin Glargine',
                'Insulin Aspart',
                'Gliclazide',
                'Glipizide'
            ],

            // Immunization
            3 => [
                'Diphtheria Vaccine',
                'Measles Vaccine',
                'Hepatitis B Vaccine',
                'Polio Vaccine',
                'COVID-19 Vaccine'
            ],

            // Family Planning Medicines
            4 => [
                'Ethinyl Estradiol',
                'Levonorgestrel',
                'Medroxyprogesterone',
                'Norethindrone',
                'Etonogestrel Implant'
            ],

            // Tuberculosis (TB) Medicines
            5 => [
                'Isoniazid',
                'Rifampicin',
                'Ethambutol',
                'Pyrazinamide',
                'Streptomycin'
            ],

            // Micronutrients and Supplements
            6 => [
                'Vitamin A',
                'Vitamin D',
                'Folic Acid',
                'Iron Sulfate',
                'Calcium'
            ],

            // IMCI Medicines
            7 => [
                'Oral Rehydration Salts',
                'Zinc Sulfate',
                'Cotrimoxazole'
            ],

            // Infectious Disease Medicines
            8 => [
                'Amoxicillin',
                'Azithromycin',
                'Ciprofloxacin',
                'Doxycycline',
                'Fluconazole'
            ],

            // Mental Health Medicines
            9 => [
                'Fluoxetine',
                'Sertraline',
                'Olanzapine',
                'Risperidone',
                'Diazepam'
            ],

            // Pain Management and Anti-inflammatory Medicines
            10 => [
                'Paracetamol',
                'Ibuprofen',
                'Diclofenac',
                'Naproxen',
                'Tramadol'
            ],

            // Cardiovascular Medicines
            11 => [
                'Aspirin',
                'Clopidogrel',
                'Bisoprolol',
                'Enalapril',
                'Hydrochlorothiazide'
            ],

            // Respiratory Medicines
            12 => [
                'Salbutamol',
                'Budesonide',
                'Montelukast',
                'Fluticasone',
                'Theophylline'
            ],

            // Gastrointestinal Medicines
            13 => [
                'Omeprazole',
                'Ranitidine',
                'Loperamide',
                'Domperidone',
                'Pantoprazole'
            ],

            // Hormonal and Endocrine Medicines
            14 => [
                'Levothyroxine',
                'Prednisone',
                'Testosterone',
                'Progesterone'
            ],

            // Neurological Medicines
            15 => [
                'Phenytoin',
                'Carbamazepine',
                'Levetiracetam',
                'Valproic Acid',
                'Gabapentin'
            ],

            // Dermatological Medicines
            16 => [
                'Clotrimazole',
                'Hydrocortisone',
                'Benzoyl Peroxide',
                'Salicylic Acid',
                'Mupirocin'
            ],

            // Oncology Medicines
            17 => [
                'Methotrexate',
                'Cyclophosphamide',
                'Doxorubicin',
                'Tamoxifen',
                'Cisplatin'
            ],

            // Oral Health
            18 => [
                'Fluoride',
                'Chlorhexidine',
                'Clindamycin',
                'Metronidazole'
            ],

            // Eye and Ear Medications
            19 => [
                'Timolol',
                'Ofloxacin',
                'Neomycin',
                'Betaxolol',
                'Brimonidine'
            ],

            // Reproductive Health Medicines
            20 => [
                'Clomiphene',
                'Human Chorionic Gonadotropin',
                'Misoprostol',
                'Mifepristone'
            ],

            // Blood Disorders Medicines
            21 => [
                'Warfarin',
                'Heparin',
                'Ferrous Sulfate',
                'Darbepoetin',
                'Epoetin Alfa'
            ],

            // Emergency and Critical Care Medicines
            22 => [
                'Epinephrine',
                'Naloxone',
                'Dopamine',
                'Atropine',
                'Amiodarone'
            ],
        ];

        foreach ($genericNames as $categoryId => $names) {
            foreach ($names as $genericName) {
                DB::table('generic_names')->insert([
                    'generic_name' => $genericName,
                    'category_id' => $categoryId,
                ]);
            }
        }
    }
}
