<?php

use Telemedizin\TelemedizinBundle\Models\Appointment;
use Telemedizin\TelemedizinBundle\Models\Doctor;

test('appointment belongs to doctor relationship', function () {
    // Arzt erstellen
    $doctor = Doctor::factory()->create();
    
    // Termin für diesen Arzt erstellen
    $appointment = Appointment::factory()->create([
        'doctor_id' => $doctor->id,
    ]);
    
    // Beziehung überprüfen
    expect($appointment->doctor)->toBeInstanceOf(Doctor::class);
    expect($appointment->doctor->id)->toBe($doctor->id);
});

test('appointment factory creates valid model', function () {
    $appointment = Appointment::factory()->create();
    
    expect($appointment)->toBeInstanceOf(Appointment::class)
        ->and($appointment->patient_name)->toBeString()
        ->and($appointment->patient_email)->toBeString()
        ->and($appointment->date_time)->toBeInstanceOf(\DateTime::class)
        ->and($appointment->status)->toBeIn(['scheduled', 'completed', 'cancelled'])
        ->and($appointment->doctor)->toBeInstanceOf(Doctor::class);
});

test('appointment has correct fillable attributes', function () {
    $appointment = new Appointment();
    
    expect($appointment->getFillable())->toContain('doctor_id')
        ->and($appointment->getFillable())->toContain('patient_name')
        ->and($appointment->getFillable())->toContain('patient_email')
        ->and($appointment->getFillable())->toContain('date_time')
        ->and($appointment->getFillable())->toContain('status');
});

test('appointment date time is properly casted', function () {
    $appointment = Appointment::factory()->create([
        'date_time' => '2023-03-15 14:30:00',
    ]);
    
    expect($appointment->date_time)->toBeInstanceOf(\DateTime::class);
    expect($appointment->date_time->format('Y-m-d H:i:s'))->toBe('2023-03-15 14:30:00');
}); 