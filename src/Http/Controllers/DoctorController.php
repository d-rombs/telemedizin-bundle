<?php

namespace Telemedizin\TelemedizinBundle\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Validation\ValidationException;
use Telemedizin\TelemedizinBundle\Models\Doctor;

class DoctorController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        $doctors = Doctor::all();
        return response()->json($doctors);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $validatedData = $request->validate([
                'name' => 'required|string|max:255',
                'specialization_id' => 'required|exists:specializations,id',
            ]);

            $doctor = Doctor::create($validatedData);
            return response()->json($doctor, Response::HTTP_CREATED);
        } catch (ValidationException $e) {
            return response()->json(['errors' => $e->errors()], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id): JsonResponse
    {
        $doctor = Doctor::with(['timeSlots' => function ($query) {
            $query->where('is_available', true)
                  ->where('start_time', '>', now())
                  ->orderBy('start_time');
        }])->findOrFail($id);
        
        return response()->json($doctor);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id): JsonResponse
    {
        try {
            $doctor = Doctor::findOrFail($id);
            
            $validatedData = $request->validate([
                'name' => 'required|string|max:255',
                'specialization_id' => 'required|exists:specializations,id',
            ]);

            $doctor->update($validatedData);
            return response()->json($doctor);
        } catch (ValidationException $e) {
            return response()->json(['errors' => $e->errors()], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id): JsonResponse
    {
        $doctor = Doctor::findOrFail($id);
        $doctor->delete();
        return response()->json(null, Response::HTTP_NO_CONTENT);
    }

    /**
     * Search for doctors by name.
     */
    public function search(Request $request): JsonResponse
    {
        $query = $request->query('query');
        
        if (!$query) {
            return response()->json([
                'data' => Doctor::all()
            ]);
        }
        
        $doctors = Doctor::where('name', 'like', "%{$query}%")->get();
        
        return response()->json([
            'data' => $doctors
        ]);
    }
} 