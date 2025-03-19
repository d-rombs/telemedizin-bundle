<?php

namespace Telemedizin\TelemedizinBundle\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Telemedizin\TelemedizinBundle\Models\Appointment;
use Telemedizin\TelemedizinBundle\Models\TimeSlot;

class AppointmentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        $appointments = Appointment::all();
        return response()->json($appointments);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $validatedData = $request->validate([
                'doctor_id' => 'required|exists:doctors,id',
                'patient_name' => 'required|string|max:255',
                'patient_email' => 'required|email|max:255',
                'date_time' => 'required|date|after:now',
                'time_slot_id' => 'required|exists:time_slots,id',
            ]);

            // Überprüfe, ob der Zeitslot verfügbar ist
            $timeSlot = TimeSlot::findOrFail($validatedData['time_slot_id']);
            
            if (!$timeSlot->is_available) {
                return response()->json([
                    'message' => 'Der gewählte Zeitslot ist nicht mehr verfügbar.'
                ], Response::HTTP_UNPROCESSABLE_ENTITY);
            }

            // Erstelle Termin
            $appointment = Appointment::create([
                'doctor_id' => $validatedData['doctor_id'],
                'patient_name' => $validatedData['patient_name'],
                'patient_email' => $validatedData['patient_email'],
                'date_time' => $validatedData['date_time'],
                'status' => 'scheduled'
            ]);

            // Markiere Zeitslot als nicht verfügbar
            $timeSlot->update(['is_available' => false]);

            // Sende Bestätigungs-E-Mail
            $this->sendConfirmationEmail($appointment);

            return response()->json($appointment, Response::HTTP_CREATED);
        } catch (ValidationException $e) {
            return response()->json(['errors' => $e->errors()], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id): JsonResponse
    {
        $appointment = Appointment::findOrFail($id);
        return response()->json($appointment);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id): JsonResponse
    {
        try {
            $appointment = Appointment::findOrFail($id);
            
            $validatedData = $request->validate([
                'status' => 'required|in:scheduled,completed,cancelled',
            ]);

            $appointment->update($validatedData);
            return response()->json($appointment);
        } catch (ValidationException $e) {
            return response()->json(['errors' => $e->errors()], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id): JsonResponse
    {
        $appointment = Appointment::findOrFail($id);
        
        // Wenn der Termin geplant ist, mache den Zeitslot wieder verfügbar
        if ($appointment->status === 'scheduled') {
            // Finde den Zeitslot, der mit diesem Termin übereinstimmt
            $timeSlot = TimeSlot::where('doctor_id', $appointment->doctor_id)
                ->where('start_time', '<=', $appointment->date_time)
                ->where('end_time', '>=', $appointment->date_time)
                ->first();
                
            if ($timeSlot) {
                $timeSlot->update(['is_available' => true]);
            }
        }
        
        $appointment->delete();
        
        return response()->json(null, Response::HTTP_NO_CONTENT);
    }
    
    /**
     * Get appointments by patient email.
     */
    public function getByEmail(string $email): JsonResponse
    {
        $appointments = Appointment::with('doctor.specialization')
            ->where('patient_email', $email)
            ->get();
            
        return response()->json($appointments);
    }
    
    /**
     * Cancel an appointment.
     */
    public function cancel(string $id): JsonResponse
    {
        $appointment = Appointment::findOrFail($id);
        
        if ($appointment->status !== 'scheduled') {
            return response()->json([
                'message' => 'Nur geplante Termine können storniert werden.'
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
        
        $appointment->update(['status' => 'cancelled']);
        
        // Mache den Zeitslot wieder verfügbar
        $timeSlot = TimeSlot::where('doctor_id', $appointment->doctor_id)
            ->where('start_time', '<=', $appointment->date_time)
            ->where('end_time', '>=', $appointment->date_time)
            ->first();
            
        if ($timeSlot) {
            $timeSlot->update(['is_available' => true]);
        }
        
        return response()->json($appointment);
    }
    
    /**
     * Send confirmation email.
     */
    private function sendConfirmationEmail(Appointment $appointment): void
    {
        try {
            // Appointment-Modell mit Beziehungen laden, falls noch nicht geladen
            if (!$appointment->relationLoaded('doctor')) {
                $appointment->load(['doctor.specialization']);
            }
            
            // E-Mail-Konfiguration aus der Konfigurationsdatei laden
            $fromEmail = config('telemedizin.from_email', 'noreply@telemedizin-beispiel.de');
            $fromName = config('telemedizin.from_name', 'Telemedizin Service');
            
            // E-Mail mit der Mailable-Klasse senden
            \Mail::to($appointment->patient_email)
                ->send(new \Telemedizin\TelemedizinBundle\Mail\AppointmentConfirmation($appointment));
                
            // Erfolgsprotokollierung
            Log::info('Terminbestätigung erfolgreich gesendet an: ' . $appointment->patient_email);
        } catch (\Exception $e) {
            // Fehlerprotokollierung
            Log::error('Fehler beim Senden der Terminbestätigung: ' . $e->getMessage());
            
            // In Produktionsumgebungen könnte hier ein Retry-Mechanismus oder eine Benachrichtigung implementiert werden
        }
    }
} 