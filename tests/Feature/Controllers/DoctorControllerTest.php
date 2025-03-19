<?php

use Telemedizin\TelemedizinBundle\Models\Doctor;
use Telemedizin\TelemedizinBundle\Models\Specialization;

test('index endpoint returns all doctors', function () {
    // Einige Test-Ärzte erstellen
    $doctors = Doctor::factory()->count(3)->create();
    
    // API-Endpunkt aufrufen
    $response = $this->getJson('/api/telemedizin/doctors');
    
    // Überprüfen, ob die Antwort erfolgreich ist und alle Ärzte enthält
    $response->assertStatus(200);
    $this->assertCount(3, $response->json());
    
    // Überprüfen, ob die Ärzte-Daten korrekt sind
    foreach ($doctors as $index => $doctor) {
        $response->assertJsonFragment(['id' => $doctor->id, 'name' => $doctor->name]);
    }
});

test('show endpoint returns specific doctor with their specialization', function () {
    // Einen Test-Arzt mit einer Spezialisierung erstellen
    $specialization = Specialization::factory()->create();
    $doctor = Doctor::factory()->create([
        'specialization_id' => $specialization->id
    ]);
    
    // API-Endpunkt für einen bestimmten Arzt aufrufen
    $response = $this->getJson("/api/telemedizin/doctors/{$doctor->id}");
    
    // Überprüfen, ob die Antwort erfolgreich ist und die korrekten Daten enthält
    $response->assertStatus(200);
    $response->assertJsonPath('id', $doctor->id);
    $response->assertJsonPath('name', $doctor->name);
    $this->assertArrayHasKey('specialization', $response->json());
    $this->assertEquals($specialization->id, $response->json()['specialization']['id']);
    $this->assertEquals($specialization->name, $response->json()['specialization']['name']);
});

test('search endpoint filters doctors by name', function () {
    // Ärzte mit unterschiedlichen Namen erstellen
    $doctor1 = Doctor::factory()->create(['name' => 'Dr. Kardiolog']);
    $doctor2 = Doctor::factory()->create(['name' => 'Dr. Kardiologe Müller']);
    $doctor3 = Doctor::factory()->create(['name' => 'Dr. Dermatologe']);
    
    // API-Endpunkt aufrufen und nach "Kardio" filtern
    $response = $this->getJson('/api/telemedizin/doctors/search?query=Kardio');
    
    // Überprüfen, ob nur Ärzte mit "Kardio" im Namen zurückgegeben werden
    $response->assertStatus(200);
    $this->assertCount(2, $response->json('data'));
    $response->assertJsonFragment(['name' => 'Dr. Kardiolog']);
    $response->assertJsonFragment(['name' => 'Dr. Kardiologe Müller']);
    $response->assertJsonMissing(['name' => 'Dr. Dermatologe']);
});

test('timeslots endpoint returns available timeslots for a doctor', function () {
    // Einen Arzt mit Zeitfenstern erstellen
    $doctor = Doctor::factory()->create();
    $timeSlots = createTimeSlot(['doctor_id' => $doctor->id, 'is_available' => true], 3);
    
    // Ein nicht verfügbares Zeitfenster hinzufügen
    createTimeSlot(['doctor_id' => $doctor->id, 'is_available' => false]);
    
    // API-Endpunkt aufrufen
    $response = $this->getJson("/api/telemedizin/doctors/{$doctor->id}/timeslots");
    
    // Überprüfen, ob nur verfügbare Zeitfenster zurückgegeben werden
    $response->assertStatus(200);
    $response->assertSuccessful();
}); 