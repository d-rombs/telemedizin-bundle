<?php

/*
|--------------------------------------------------------------------------
| Test Case
|--------------------------------------------------------------------------
|
| Die folgende Klasse definiert einen grundlegenden Testfall für unser Bundle.
|
*/

use Telemedizin\TelemedizinBundle\Tests\TestCase;

uses(TestCase::class)->in('Feature', 'Unit');

/*
|--------------------------------------------------------------------------
| Erwartungsfunktionen
|--------------------------------------------------------------------------
|
| Eigene expect-Funktionen für verbesserte Test-Ausdruckskraft hinzufügen.
|
*/

/*
expect()->extend('toBeOne', function () {
    return $this->toBe(1);
});
*/


/*
|--------------------------------------------------------------------------
| Funktionen
|--------------------------------------------------------------------------
|
| Funktionen, die im Testing-Kontext nützlich sein können, hier definieren.
|
*/

function createDoctor($attributes = [], $count = 1)
{
    return $count > 1 
        ? \Telemedizin\TelemedizinBundle\Models\Doctor::factory()->count($count)->create($attributes)
        : \Telemedizin\TelemedizinBundle\Models\Doctor::factory()->create($attributes);
}

function createTimeSlot($attributes = [], $count = 1)
{
    return $count > 1 
        ? \Telemedizin\TelemedizinBundle\Models\TimeSlot::factory()->count($count)->create($attributes)
        : \Telemedizin\TelemedizinBundle\Models\TimeSlot::factory()->create($attributes);
}

function createAppointment($attributes = [], $count = 1)
{
    return $count > 1 
        ? \Telemedizin\TelemedizinBundle\Models\Appointment::factory()->count($count)->create($attributes)
        : \Telemedizin\TelemedizinBundle\Models\Appointment::factory()->create($attributes);
}

// Define group tags for all tests
uses()->group('feature')->in('Feature');
uses()->group('unit')->in('Unit');