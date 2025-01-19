<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        $this->call([
            MedicalCategoriesTableSeeder::class,
            GenericNamesTableSeeder::class,
            BatchesTableSeeder::class,
            RhusTableSeeder::class,
            RolesTableSeeder::class,
            BarangaysTableSeeder::class,
            UsersTableSeeder::class,
            MedicinesTableSeeder::class,
            EquipmentsTableSeeder::class,
            MedicalSuppliesTableSeeder::class,
            MidwifeInventorySeeder::class,
        ]);
    }
}
