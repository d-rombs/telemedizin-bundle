<?php

use Telemedizin\TelemedizinBundle\Models\Doctor;
use Telemedizin\TelemedizinBundle\Models\Specialization;
use Telemedizin\TelemedizinBundle\Models\Appointment;
use Telemedizin\TelemedizinBundle\Models\TimeSlot;

test('doctor has specialization relationship', function () {
    // Spezialisierung erstellen
    $specialization = Specialization::factory()->create();
    
    // Arzt mit dieser Spezialisierung erstellen
    $doctor = Doctor::factory()->create([
        'specialization_id' => $specialization->id,
    ]);
    
    // Beziehung überprüfen
    expect($doctor->specialization)->toBeInstanceOf(Specialization::class);
    expect($doctor->specialization->id)->toBe($specialization->id);
});

test('doctor has appointments relationship', function () {
    // Arzt erstellen
    $doctor = Doctor::factory()->create();
    
    // Termine für diesen Arzt erstellen
    $appointments = Appointment::factory()->count(3)->create([
        'doctor_id' => $doctor->id,
    ]);
    
    // Beziehung überprüfen
    expect($doctor->appointments)->toHaveCount(3);
    expect($doctor->appointments->first())->toBeInstanceOf(Appointment::class);
});

test('doctor has time slots relationship', function () {
    // Arzt erstellen
    $doctor = Doctor::factory()->create();
    
    // Zeitslots für diesen Arzt erstellen
    $timeSlots = TimeSlot::factory()->count(5)->create([
        'doctor_id' => $doctor->id,
    ]);
    
    // Beziehung überprüfen
    expect($doctor->timeSlots)->toHaveCount(5);
    expect($doctor->timeSlots->first())->toBeInstanceOf(TimeSlot::class);
});

test('doctor factory creates valid model', function () {
    $doctor = Doctor::factory()->create();
    
    expect($doctor)->toBeInstanceOf(Doctor::class)
        ->and($doctor->name)->toBeString()
        ->and($doctor->specialization)->toBeInstanceOf(Specialization::class);
});

test('doctor has correct fillable attributes', function () {
    $doctor = new Doctor();
    
    expect($doctor->getFillable())->toContain('name')
        ->and($doctor->getFillable())->toContain('specialization_id');
}); 