<?php

namespace Telemedizin\TelemedizinBundle\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Specialization extends Model
{
    use HasFactory;

    /**
     * Erstelle eine neue Factory-Instanz für das Modell.
     */
    protected static function newFactory()
    {
        return \Telemedizin\TelemedizinBundle\Database\Factories\SpecializationFactory::new();
    }

    /**
     * Die Attribute, die zuweisbar sind.
     *
     * @var array<int, string>
     */
    protected $fillable = ['name'];

    /**
     * Die Attribute, die versteckt werden sollen.
     *
     * @var array<int, string>
     */
    protected $hidden = ['created_at', 'updated_at'];

    /**
     * Get the doctors that belong to this specialization.
     */
    public function doctors(): HasMany
    {
        return $this->hasMany(Doctor::class);
    }
} 