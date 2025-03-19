<?php

namespace Telemedizin\TelemedizinBundle\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Telemedizin\TelemedizinBundle\Models\Doctor;
use Telemedizin\TelemedizinBundle\Models\Specialization;

class DoctorFactory extends Factory
{
    protected $model = Doctor::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => 'Dr. ' . $this->faker->name(),
            'specialization_id' => Specialization::factory(),
        ];
    }
} 