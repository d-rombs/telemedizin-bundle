<?php

namespace Telemedizin\TelemedizinBundle\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Telemedizin\TelemedizinBundle\Models\Doctor;
use Telemedizin\TelemedizinBundle\Models\TimeSlot;

class TimeSlotFactory extends Factory
{
    protected $model = TimeSlot::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $startTime = $this->faker->dateTimeBetween('now', '+1 month');
        $endTime = (clone $startTime)->modify('+30 minutes');
        
        return [
            'doctor_id' => Doctor::factory(),
            'start_time' => $startTime,
            'end_time' => $endTime,
            'is_available' => $this->faker->boolean(80), // 80% chance of being available
        ];
    }
} 