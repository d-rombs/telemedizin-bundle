<?php

use Illuminate\Support\Facades\Route;
use Telemedizin\TelemedizinBundle\Http\Controllers\AppointmentController;
use Telemedizin\TelemedizinBundle\Http\Controllers\DoctorController;
use Telemedizin\TelemedizinBundle\Http\Controllers\SpecializationController;
use Telemedizin\TelemedizinBundle\Http\Controllers\TimeSlotController;

Route::group(['prefix' => config('telemedizin.routes.prefix', 'api/telemedizin'), 'middleware' => config('telemedizin.routes.middleware', ['api'])], function () {
    // Specialization Routes
    Route::apiResource('specializations', SpecializationController::class);
    
    // Doctor Routes
    Route::get('doctors/search', [DoctorController::class, 'search']);
    Route::apiResource('doctors', DoctorController::class);
    
    // TimeSlot Routes
    Route::apiResource('timeslots', TimeSlotController::class);
    Route::get('doctors/{doctor}/timeslots', [TimeSlotController::class, 'getByDoctor']);
    Route::post('doctors/{doctor}/timeslots/generate', [TimeSlotController::class, 'generateForDoctor']);
    Route::get('timeslots/check-availability/{id}', [TimeSlotController::class, 'checkAvailability']);
    
    // Appointment Routes
    Route::apiResource('appointments', AppointmentController::class);
    Route::get('appointments/patient/{email}', [AppointmentController::class, 'getByEmail']);
    Route::patch('appointments/{appointment}/cancel', [AppointmentController::class, 'cancel']);
}); 