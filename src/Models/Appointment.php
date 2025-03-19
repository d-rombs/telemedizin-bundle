<?php

namespace Telemedizin\TelemedizinBundle\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Appointment extends Model
{
    use HasFactory;

    /**
     * Erstelle eine neue Factory-Instanz für das Modell.
     */
    protected static function newFactory()
    {
        return \Telemedizin\TelemedizinBundle\Database\Factories\AppointmentFactory::new();
    }

    /**
     * Die Attribute, die zuweisbar sind.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'doctor_id',
        'patient_name',
        'patient_email',
        'date_time',
        'status'
    ];

    /**
     * Die Attributtypen, die gecastet werden sollen.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'date_time' => 'datetime',
    ];

    /**
     * Die standardmäßig mit diesem Modell zu ladenden Beziehungen.
     *
     * @var array<int, string>
     */
    protected $with = ['doctor'];

    /**
     * Die Attribute, die versteckt werden sollen.
     *
     * @var array<int, string>
     */
    protected $hidden = ['created_at', 'updated_at'];

    /**
     * Get the doctor that owns the appointment.
     */
    public function doctor(): BelongsTo
    {
        return $this->belongsTo(Doctor::class);
    }
} 