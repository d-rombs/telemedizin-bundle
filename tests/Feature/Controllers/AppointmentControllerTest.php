<?php

use Telemedizin\TelemedizinBundle\Models\Appointment;
use Telemedizin\TelemedizinBundle\Models\Doctor;
use Telemedizin\TelemedizinBundle\Models\TimeSlot;
use Illuminate\Support\Carbon;

test('index endpoint returns all appointments', function () {
    // Einige Test-Termine erstellen
    $appointments = Appointment::factory()->count(3)->create([
        'status' => 'scheduled' // Gültiger Enum-Wert
    ]);
    
    // API-Endpunkt aufrufen
    $response = $this->getJson('/api/telemedizin/appointments');
    
    // Überprüfen, ob die Antwort erfolgreich ist und alle Termine enthält
    $response->assertStatus(200);
    $this->assertCount(3, $response->json());
});

test('store endpoint creates a new appointment', function () {
    // Einen Arzt und ein verfügbares Zeitfenster erstellen
    $doctor = Doctor::factory()->create();
    $timeSlot = TimeSlot::factory()->create([
        'doctor_id' => $doctor->id,
        'is_available' => true,
        'start_time' => Carbon::now()->addDay()->setHour(9)->setMinute(0),
        'end_time' => Carbon::now()->addDay()->setHour(9)->setMinute(30),
    ]);
    
    // Daten für einen neuen Termin
    $appointmentData = [
        'doctor_id' => $doctor->id,
        'time_slot_id' => $timeSlot->id,
        'patient_name' => 'Test Patient',
        'patient_email' => 'patient@example.com',
        'date_time' => Carbon::now()->addDay()->setHour(9)->setMinute(0)->format('Y-m-d H:i:s'),
    ];
    
    // API-Endpunkt aufrufen und neuen Termin erstellen
    $response = $this->postJson('/api/telemedizin/appointments', $appointmentData);
    
    // Überprüfen, ob der Termin erfolgreich erstellt wurde
    $response->assertStatus(201);
    $this->assertEquals('Test Patient', $response->json()['patient_name']);
    $this->assertEquals('patient@example.com', $response->json()['patient_email']);
    
    // Überprüfen, ob der Zeitslot als nicht mehr verfügbar markiert wurde
    $this->assertFalse(TimeSlot::find($timeSlot->id)->is_available);
});

test('update endpoint changes appointment status', function () {
    // Einen Termin erstellen
    $doctor = Doctor::factory()->create();
    $appointment = Appointment::factory()->create([
        'doctor_id' => $doctor->id,
        'status' => 'scheduled' // Gültiger Enum-Wert
    ]);
    
    // API-Endpunkt aufrufen, um den Status zu aktualisieren
    $response = $this->putJson("/api/telemedizin/appointments/{$appointment->id}", [
        'status' => 'completed'
    ]);
    
    // Überprüfen, ob der Termin aktualisiert wurde
    $response->assertStatus(200);
    $this->assertEquals('completed', $response->json()['status']);
    
    // Überprüfen, ob der Status in der Datenbank aktualisiert wurde
    $this->assertEquals('completed', Appointment::find($appointment->id)->status);
});

test('destroy endpoint cancels an appointment', function () {
    // Einen Termin erstellen
    $doctor = Doctor::factory()->create();
    $timeSlot = TimeSlot::factory()->create([
        'doctor_id' => $doctor->id,
        'is_available' => false
    ]);
    
    $appointment = Appointment::factory()->create([
        'doctor_id' => $doctor->id,
        'status' => 'scheduled',
        'date_time' => $timeSlot->start_time
    ]);
    
    // API-Endpunkt aufrufen, um den Termin zu stornieren
    $response = $this->deleteJson("/api/telemedizin/appointments/{$appointment->id}");
    
    // Überprüfen, ob der Termin erfolgreich storniert wurde
    $response->assertStatus(204);
    
    // Überprüfen, ob der Termin aus der Datenbank gelöscht wurde
    $this->assertNull(Appointment::find($appointment->id));
}); 