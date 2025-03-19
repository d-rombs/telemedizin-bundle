<?php

use Telemedizin\TelemedizinBundle\Models\TimeSlot;
use Telemedizin\TelemedizinBundle\Models\Doctor;

test('time slot belongs to doctor relationship', function () {
    // Arzt erstellen
    $doctor = Doctor::factory()->create();
    
    // Zeitslot für diesen Arzt erstellen
    $timeSlot = TimeSlot::factory()->create([
        'doctor_id' => $doctor->id,
    ]);
    
    // Beziehung überprüfen
    expect($timeSlot->doctor)->toBeInstanceOf(Doctor::class);
    expect($timeSlot->doctor->id)->toBe($doctor->id);
});

test('time slot factory creates valid model', function () {
    $timeSlot = TimeSlot::factory()->create();
    
    expect($timeSlot)->toBeInstanceOf(TimeSlot::class)
        ->and($timeSlot->start_time)->toBeInstanceOf(\DateTime::class)
        ->and($timeSlot->end_time)->toBeInstanceOf(\DateTime::class)
        ->and($timeSlot->is_available)->toBeBool()
        ->and($timeSlot->doctor)->toBeInstanceOf(Doctor::class);
});

test('time slot has correct fillable attributes', function () {
    $timeSlot = new TimeSlot();
    
    expect($timeSlot->getFillable())->toContain('doctor_id')
        ->and($timeSlot->getFillable())->toContain('start_time')
        ->and($timeSlot->getFillable())->toContain('end_time')
        ->and($timeSlot->getFillable())->toContain('is_available');
});

test('time slot dates are properly casted', function () {
    $timeSlot = TimeSlot::factory()->create([
        'start_time' => '2023-03-15 14:00:00',
        'end_time' => '2023-03-15 14:30:00',
    ]);
    
    expect($timeSlot->start_time)->toBeInstanceOf(\DateTime::class);
    expect($timeSlot->start_time->format('Y-m-d H:i:s'))->toBe('2023-03-15 14:00:00');
    
    expect($timeSlot->end_time)->toBeInstanceOf(\DateTime::class);
    expect($timeSlot->end_time->format('Y-m-d H:i:s'))->toBe('2023-03-15 14:30:00');
});

test('is_available is properly casted to boolean', function () {
    $timeSlot = TimeSlot::factory()->create([
        'is_available' => true,
    ]);
    
    expect($timeSlot->is_available)->toBeBool();
    expect($timeSlot->is_available)->toBeTrue();
    
    $timeSlot->update(['is_available' => false]);
    $timeSlot->refresh();
    
    expect($timeSlot->is_available)->toBeBool();
    expect($timeSlot->is_available)->toBeFalse();
}); 