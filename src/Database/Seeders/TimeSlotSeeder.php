<?php

namespace Telemedizin\TelemedizinBundle\Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Telemedizin\TelemedizinBundle\Models\Doctor;
use Telemedizin\TelemedizinBundle\Models\TimeSlot;

class TimeSlotSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $doctors = Doctor::all();
        
        // Generiere Zeitslots für die nächsten 7 Tage
        for ($day = 0; $day < 7; $day++) {
            $date = Carbon::now()->addDays($day)->setHour(8)->setMinute(0)->setSecond(0);
            
            // Zeitslots von 8:00 bis 18:00 Uhr mit 30-minütigen Intervallen
            for ($hour = 8; $hour < 17; $hour++) {
                for ($minute = 0; $minute < 60; $minute += 30) {
                    $startTime = (clone $date)->setHour($hour)->setMinute($minute);
                    $endTime = (clone $startTime)->addMinutes(30);
                    
                    // Erstelle Zeitslots für alle Ärzte
                    foreach ($doctors as $doctor) {
                        // 90% Verfügbarkeit für die Slots
                        $isAvailable = rand(1, 10) > 1;
                        
                        TimeSlot::create([
                            'doctor_id' => $doctor->id,
                            'start_time' => $startTime,
                            'end_time' => $endTime,
                            'is_available' => $isAvailable,
                        ]);
                    }
                }
            }
        }
    }
} 