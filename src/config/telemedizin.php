<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Telemedizin Konfiguration
    |--------------------------------------------------------------------------
    |
    | Diese Konfiguration wird von dem Telemedizin-Bundle verwendet.
    |
    */

    // Anwendungsname, der in E-Mails angezeigt wird
    'app_name' => env('TELEMEDIZIN_APP_NAME', 'Telemedizin-Plattform'),

    // URL der Anwendung für Links in E-Mails
    'app_url' => env('TELEMEDIZIN_APP_URL', env('APP_URL', 'http://localhost:8000')),

    // E-Mail für Kontaktaufnahme
    'contact_email' => env('TELEMEDIZIN_CONTACT_EMAIL', 'kontakt@telemedizin-beispiel.de'),

    // Absender-E-Mail für System-E-Mails
    'from_email' => env('TELEMEDIZIN_FROM_EMAIL', 'noreply@telemedizin-beispiel.de'),

    // Absender-Name für System-E-Mails
    'from_name' => env('TELEMEDIZIN_FROM_NAME', 'Telemedizin Service'),
    
    // Zeitslot-Einstellungen
    'time_slots' => [
        'duration' => 30, // Standarddauer eines Zeitslots in Minuten - telemedizin.time_slots.duration
        'workday_start' => '08:00', // Beginn des Arbeitstages - telemedizin.time_slots.workday_start
        'workday_end' => '18:00', // Ende des Arbeitstages - telemedizin.time_slots.workday_end
    ],

    // Routen-Präfix
    'routes' => [
        'prefix' => 'api/telemedizin',
        'middleware' => ['api'],
    ],
    
    
]; 