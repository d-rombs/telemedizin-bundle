<?php

namespace Telemedizin\TelemedizinBundle\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TimeSlot extends Model
{
    use HasFactory;

    /**
     * Erstelle eine neue Factory-Instanz für das Modell.
     */
    protected static function newFactory()
    {
        return \Telemedizin\TelemedizinBundle\Database\Factories\TimeSlotFactory::new();
    }

    /**
     * Die Attribute, die zuweisbar sind.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'doctor_id',
        'start_time',
        'end_time',
        'is_available'
    ];

    /**
     * Die Attributtypen, die gecastet werden sollen.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'start_time' => 'datetime',
        'end_time' => 'datetime',
        'is_available' => 'boolean',
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
     * Get the doctor that owns the time slot.
     */
    public function doctor(): BelongsTo
    {
        return $this->belongsTo(Doctor::class);
    }
} 