<?php

namespace Telemedizin\TelemedizinBundle\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Telemedizin\TelemedizinBundle\Models\Appointment;
use Telemedizin\TelemedizinBundle\Models\Doctor;
use Telemedizin\TelemedizinBundle\Models\TimeSlot;

class AppointmentFactory extends Factory
{
    protected $model = Appointment::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'doctor_id' => Doctor::factory(),
            'patient_name' => $this->faker->name(),
            'patient_email' => $this->faker->email(),
            'date_time' => $this->faker->dateTimeBetween('now', '+1 month'),
            'status' => $this->faker->randomElement(['scheduled', 'completed', 'cancelled']),
        ];
    }
} 