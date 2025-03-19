<?php

namespace Telemedizin\TelemedizinBundle\Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            SpecializationSeeder::class,
            DoctorSeeder::class,
            TimeSlotSeeder::class,
        ]);
    }
} 