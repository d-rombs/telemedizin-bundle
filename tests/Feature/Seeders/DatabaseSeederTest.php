<?php

use Telemedizin\TelemedizinBundle\Database\Seeders\DatabaseSeeder;
use Telemedizin\TelemedizinBundle\Models\Specialization;
use Telemedizin\TelemedizinBundle\Models\Doctor;
use Telemedizin\TelemedizinBundle\Models\TimeSlot;

test('database seeder creates all required records', function () {
    // Vor dem Seeden sicherstellen, dass die Tabellen leer sind
    expect(Specialization::count())->toBe(0);
    expect(Doctor::count())->toBe(0);
    expect(TimeSlot::count())->toBe(0);
    
    // Seeder ausführen
    $seeder = new DatabaseSeeder();
    $seeder->run();
    
    // Nach dem Seeden überprüfen, ob Daten erstellt wurden
    expect(Specialization::count())->toBeGreaterThan(0);
    expect(Doctor::count())->toBeGreaterThan(0);
    expect(TimeSlot::count())->toBeGreaterThan(0);
});

test('specialization seeder creates expected specializations', function () {
    // Datenbank seeden
    $seeder = new DatabaseSeeder();
    $seeder->run();
    
    // Überprüfen, ob alle erwarteten Spezialisierungen erstellt wurden
    $expectedSpecializations = [
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
    ];
    
    foreach ($expectedSpecializations as $specialization) {
        expect(Specialization::where('name', $specialization)->exists())->toBeTrue();
    }
});

test('doctor seeder creates expected doctors', function () {
    // Datenbank seeden
    $seeder = new DatabaseSeeder();
    $seeder->run();
    
    // Überprüfen, ob alle Ärzte eine gültige Spezialisierung haben
    $doctors = Doctor::all();
    expect($doctors)->toHaveCount(15); // Basierend auf den 15 Ärzten im Seeder
    
    foreach ($doctors as $doctor) {
        expect($doctor->specialization)->not->toBeNull();
    }
}); 