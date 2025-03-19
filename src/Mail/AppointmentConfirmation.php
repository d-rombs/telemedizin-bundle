<?php

namespace Telemedizin\TelemedizinBundle\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Telemedizin\TelemedizinBundle\Models\Appointment;

class AppointmentConfirmation extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Der Termin, der bestätigt werden soll.
     *
     * @var Appointment
     */
    public $appointment;

    /**
     * Erstelle eine neue Nachrichteninstanz.
     *
     * @param Appointment $appointment
     * @return void
     */
    public function __construct(Appointment $appointment)
    {
        $this->appointment = $appointment;
        $this->subject('Terminbestätigung: Ihr Termin wurde erfolgreich gebucht');
    }

    /**
     * Erstelle die Nachricht.
     *
     * @return $this
     */
    public function build()
    {
        return $this->markdown('telemedizin::emails.appointment-confirmation')
                    ->with([
                        'patientName' => $this->appointment->patient_name,
                        'doctorName' => $this->appointment->doctor->name,
                        'dateTime' => $this->appointment->date_time->format('d.m.Y H:i'),
                        'specialization' => $this->appointment->doctor->specialization->name ?? 'Medizinischer Spezialist'
                    ]);
    }
} 