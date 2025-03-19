<?php

use Telemedizin\TelemedizinBundle\Models\Specialization;
use Telemedizin\TelemedizinBundle\Models\Doctor;

test('specialization has doctors relationship', function () {
    // Spezialisierung erstellen
    $specialization = Specialization::factory()->create();
    
    // Ärzte mit dieser Spezialisierung erstellen
    Doctor::factory()->count(3)->create([
        'specialization_id' => $specialization->id,
    ]);
    
    // Beziehung überprüfen
    expect($specialization->doctors)->toHaveCount(3);
    expect($specialization->doctors->first())->toBeInstanceOf(Doctor::class);
});

test('specialization factory creates valid model', function () {
    $specialization = Specialization::factory()->create();
    
    expect($specialization)->toBeInstanceOf(Specialization::class)
        ->and($specialization->name)->toBeString();
});

test('specialization has correct fillable attributes', function () {
    $specialization = new Specialization();
    
    expect($specialization->getFillable())->toContain('name');
}); 