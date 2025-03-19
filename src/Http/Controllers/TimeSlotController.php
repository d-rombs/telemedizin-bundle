<?php

namespace Telemedizin\TelemedizinBundle\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Validation\ValidationException;
use Telemedizin\TelemedizinBundle\Models\Doctor;
use Telemedizin\TelemedizinBundle\Models\TimeSlot;

class TimeSlotController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        $timeSlots = TimeSlot::where('is_available', true)
            ->where('start_time', '>', now())
            ->orderBy('start_time')
            ->get();
            
        return response()->json($timeSlots);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $validatedData = $request->validate([
                'doctor_id' => 'required|exists:doctors,id',
                'start_time' => 'required|date|after:now',
                'end_time' => 'required|date|after:start_time',
                'is_available' => 'boolean',
            ]);

            // Prüfe auf Überschneidungen
            $overlapping = TimeSlot::where('doctor_id', $validatedData['doctor_id'])
                ->where(function ($query) use ($validatedData) {
                    $query->whereBetween('start_time', [$validatedData['start_time'], $validatedData['end_time']])
                        ->orWhereBetween('end_time', [$validatedData['start_time'], $validatedData['end_time']])
                        ->orWhere(function ($q) use ($validatedData) {
                            $q->where('start_time', '<=', $validatedData['start_time'])
                                ->where('end_time', '>=', $validatedData['end_time']);
                        });
                })
                ->exists();

            if ($overlapping) {
                return response()->json([
                    'message' => 'Der Zeitslot überschneidet sich mit einem bestehenden Zeitslot.'
                ], Response::HTTP_UNPROCESSABLE_ENTITY);
            }
            
            $timeSlot = TimeSlot::create($validatedData);
            return response()->json($timeSlot, Response::HTTP_CREATED);
        } catch (ValidationException $e) {
            return response()->json(['errors' => $e->errors()], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id): JsonResponse
    {
        $timeSlot = TimeSlot::findOrFail($id);
        return response()->json($timeSlot);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id): JsonResponse
    {
        try {
            $timeSlot = TimeSlot::findOrFail($id);
            
            $validatedData = $request->validate([
                'doctor_id' => 'exists:doctors,id',
                'start_time' => 'date|after:now',
                'end_time' => 'date|after:start_time',
                'is_available' => 'boolean',
            ]);

            if (isset($validatedData['doctor_id']) || isset($validatedData['start_time']) || isset($validatedData['end_time'])) {
                // Wenn Zeit oder Arzt geändert wird, prüfe auf Überschneidungen
                $doctorId = $validatedData['doctor_id'] ?? $timeSlot->doctor_id;
                $startTime = $validatedData['start_time'] ?? $timeSlot->start_time;
                $endTime = $validatedData['end_time'] ?? $timeSlot->end_time;
                
                $overlapping = TimeSlot::where('doctor_id', $doctorId)
                    ->where('id', '!=', $id)
                    ->where(function ($query) use ($startTime, $endTime) {
                        $query->whereBetween('start_time', [$startTime, $endTime])
                            ->orWhereBetween('end_time', [$startTime, $endTime])
                            ->orWhere(function ($q) use ($startTime, $endTime) {
                                $q->where('start_time', '<=', $startTime)
                                    ->where('end_time', '>=', $endTime);
                            });
                    })
                    ->exists();
    
                if ($overlapping) {
                    return response()->json([
                        'message' => 'Der Zeitslot überschneidet sich mit einem bestehenden Zeitslot.'
                    ], Response::HTTP_UNPROCESSABLE_ENTITY);
                }
            }

            $timeSlot->update($validatedData);
            return response()->json($timeSlot);
        } catch (ValidationException $e) {
            return response()->json(['errors' => $e->errors()], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id): JsonResponse
    {
        $timeSlot = TimeSlot::findOrFail($id);
        $timeSlot->delete();
        return response()->json(null, Response::HTTP_NO_CONTENT);
    }

    /**
     * Get time slots for a specific doctor.
     */
    public function getByDoctor(string $doctorId): JsonResponse
    {
        $doctor = Doctor::findOrFail($doctorId);
        
        $timeSlots = $doctor->timeSlots()
            ->where('is_available', true)
            ->where('start_time', '>', now())
            ->orderBy('start_time')
            ->get();
            
        return response()->json($timeSlots);
    }

    /**
     * Generate time slots for a doctor.
     */
    public function generateForDoctor(Request $request, string $doctorId): JsonResponse
    {
        try {
            $doctor = Doctor::findOrFail($doctorId);
            
            $validatedData = $request->validate([
                'start_date' => 'required|date|after_or_equal:today',
                'end_date' => 'required|date|after_or_equal:start_date',
                'slot_duration' => 'integer|min:15|max:120',
                'start_time' => 'required|date_format:H:i',
                'end_time' => 'required|date_format:H:i|after:start_time',
                'days_of_week' => 'array',
                'days_of_week.*' => 'integer|between:0,6',
            ]);

            $startDate = Carbon::parse($validatedData['start_date']);
            $endDate = Carbon::parse($validatedData['end_date']);
            $slotDuration = $validatedData['slot_duration'] ?? config('telemedizin.time_slots.duration', 30);
            $startTime = $validatedData['start_time'] ?? config('telemedizin.time_slots.duration', '8:00');
            $endTime = $validatedData['end_time'] ?? config('telemedizin.time_slots.duration', '18:00');
            $daysOfWeek = $validatedData['days_of_week'] ?? [1, 2, 3, 4, 5]; // Mo-Fr standardmäßig
            
            $createdSlots = [];
            
            for ($date = $startDate; $date->lte($endDate); $date->addDay()) {
                // Überprüfe, ob der aktuelle Tag im ausgewählten Tag der Woche ist
                if (!in_array($date->dayOfWeek, $daysOfWeek)) {
                    continue;
                }
                
                $currentTime = Carbon::parse($date->format('Y-m-d') . ' ' . $startTime);
                $dailyEndTime = Carbon::parse($date->format('Y-m-d') . ' ' . $endTime);
                
                while ($currentTime->lt($dailyEndTime)) {
                    $slotEndTime = (clone $currentTime)->addMinutes($slotDuration);
                    
                    // Stelle sicher, dass der Slot nicht über die Endzeit hinausgeht
                    if ($slotEndTime->gt($dailyEndTime)) {
                        break;
                    }
                    
                    // Prüfe auf Überschneidungen
                    $overlapping = TimeSlot::where('doctor_id', $doctorId)
                        ->where(function ($query) use ($currentTime, $slotEndTime) {
                            $query->whereBetween('start_time', [$currentTime, $slotEndTime])
                                ->orWhereBetween('end_time', [$currentTime, $slotEndTime])
                                ->orWhere(function ($q) use ($currentTime, $slotEndTime) {
                                    $q->where('start_time', '<=', $currentTime)
                                        ->where('end_time', '>=', $slotEndTime);
                                });
                        })
                        ->exists();
                    
                    if (!$overlapping) {
                        $timeSlot = TimeSlot::create([
                            'doctor_id' => $doctorId,
                            'start_time' => $currentTime,
                            'end_time' => $slotEndTime,
                            'is_available' => true
                        ]);
                        
                        $createdSlots[] = $timeSlot;
                    }
                    
                    $currentTime->addMinutes($slotDuration);
                }
            }
            
            return response()->json($createdSlots, Response::HTTP_CREATED);
        } catch (ValidationException $e) {
            return response()->json(['errors' => $e->errors()], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
    }

    /**
     * Check if a time slot is still available in real-time.
     */
    public function checkAvailability(string $id): JsonResponse
    {
        try {
            $timeSlot = TimeSlot::findOrFail($id);
            
            // Check if the time slot is in the past
            if (Carbon::parse($timeSlot->start_time)->isPast()) {
                return response()->json([
                    'is_available' => false,
                    'message' => 'Der Zeitslot liegt in der Vergangenheit.'
                ]);
            }
            
            return response()->json([
                'is_available' => $timeSlot->is_available,
                'time_slot' => $timeSlot,
                'last_updated' => now()->toIso8601String()
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'is_available' => false,
                'message' => 'Der Zeitslot konnte nicht gefunden werden.'
            ], Response::HTTP_NOT_FOUND);
        }
    }
} 