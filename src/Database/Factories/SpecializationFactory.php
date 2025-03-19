<?php

namespace Telemedizin\TelemedizinBundle\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Telemedizin\TelemedizinBundle\Models\Specialization;

class SpecializationFactory extends Factory
{
    protected $model = Specialization::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->unique()->randomElement([
                'Allgemeinmedizin',
                'Kardiologie',
                'Dermatologie',
                'Neurologie',
                'Orthopädie',
                'Psychiatrie',
                'Gynäkologie',
                'Urologie',
                'Pädiatrie',
                'Augenheilkunde',
            ]),
        ];
    }
} 