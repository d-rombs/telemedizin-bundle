# Telemedizin Bundle für Laravel

Ein wiederverwendbares Laravel-Paket für Telemedizin-Anwendungen, das Funktionen für die Verwaltung von Ärzten, deren Fachbereichen, Zeitslots und Terminen bereitstellt.




## Installation

Sie können das Paket über Composer installieren:

```bash
composer require telemedizin/telemedizin-bundle
```

## Konfiguration

Nach der Installation veröffentlichen Sie die Konfigurationsdatei mit:

```bash
php artisan vendor:publish --provider="Telemedizin\TelemedizinBundle\TelemedizinServiceProvider" --tag="telemedizin-config"
```

Dies erstellt eine Konfigurationsdatei `config/telemedizin.php`, die Sie anpassen können:

```php
return [
    // Zeitslot-Einstellungen
    'time_slots' => [
        'duration' => 30, // Standarddauer eines Zeitslots in Minuten
        'workday_start' => '08:00', // Beginn des Arbeitstages
        'workday_end' => '18:00', // Ende des Arbeitstages
    ],

    // Termin-Einstellungen
    'appointments' => [
        'max_per_day' => 10, // Maximale Anzahl an Terminen pro Tag für einen Arzt
        'status_types' => [
            'scheduled',
            'completed',
            'cancelled'
        ],
    ],

    // Routen-Präfix
    'routes' => [
        'prefix' => 'api/telemedizin',
        'middleware' => ['api'],
    ],
];
```

## Migrations

Veröffentlichen Sie die Migrations mit:

```bash
php artisan vendor:publish --provider="Telemedizin\TelemedizinBundle\TelemedizinServiceProvider" --tag="telemedizin-migrations"
```

Führen Sie dann die Migrationen aus:

```bash
php artisan migrate
```

optional ist auch möglich Seeders auszuführen:

```bash
php artisan telemedizin:seed
```

## E-Mail-Funktionalität

Das Bundle unterstützt das Versenden von E-Mail-Benachrichtigungen für verschiedene Ereignisse:

### Konfiguration

Die E-Mail-Konfiguration erfolgt über die `telemedizin.php` Konfigurationsdatei. Folgende Parameter können angepasst werden:

- `from_email`: Die Absender-E-Mail-Adresse (Standard: `noreply@telemedizin-beispiel.de`)
- `from_name`: Der Absendername (Standard: `Telemedizin Service`)
- `app_name`: Der Anwendungsname für die E-Mail-Fußzeile
- `app_url`: Die URL der Anwendung für Buttons in E-Mails
- `contact_email`: Die Kontakt-E-Mail-Adresse für Rückfragen

### Verfügbare E-Mail-Vorlagen

- `AppointmentConfirmation`: Wird automatisch versendet, wenn ein neuer Termin gebucht wird

### Veröffentlichung der E-Mail-Vorlagen

Um die E-Mail-Vorlagen anzupassen, können Sie diese in Ihre Laravel-Anwendung veröffentlichen:

```bash
php artisan vendor:publish --tag=telemedizin-email-templates
```

Danach können Sie die Vorlagen unter `resources/views/vendor/telemedizin/emails/` bearbeiten.

### Manuelle Verwendung

Die E-Mail-Klassen können auch manuell verwendet werden:

```php
use Telemedizin\TelemedizinBundle\Mail\AppointmentConfirmation;
use Telemedizin\TelemedizinBundle\Models\Appointment;

// Termin laden
$appointment = Appointment::find($id);

// E-Mail versenden
\Mail::to($appointment->patient_email)
    ->send(new AppointmentConfirmation($appointment));
```

### MailHog für Entwicklung

Für die lokale Entwicklung empfehlen wir die Verwendung von [MailHog](https://github.com/mailhog/MailHog), um E-Mails abzufangen und in einer Weboberfläche anzuzeigen. Die mitgelieferte `docker-compose.override.yml` Datei enthält bereits die entsprechende Konfiguration.

## Funktionen

Das Bundle enthält die folgenden Hauptfunktionen:

### Fachbereiche (Specializations)

- Verwaltung von medizinischen Fachbereichen
- CRUD-Operationen über REST API

### Ärzte (Doctors)

- Verwaltung von Ärzten und deren Fachbereichen
- CRUD-Operationen über REST API

### Zeitslots (TimeSlots)

- Verfügbare Zeitslots für Ärzte
- Bulk-Generierung von Zeitslots für einen Arzt
- Überprüfung auf Überschneidungen
- CRUD-Operationen über REST API

### Termine (Appointments)

- Terminvereinbarung mit Ärzten
- Status-Management (geplant, abgeschlossen, storniert)
- Validierung gegen verfügbare Zeitslots
- CRUD-Operationen über REST API

## API-Routen

Das Bundle stellt die folgenden API-Endpunkte bereit:

### Fachbereiche

- `GET /api/telemedizin/specializations` - Alle Fachbereiche abrufen
- `POST /api/telemedizin/specializations` - Neuen Fachbereich erstellen
- `GET /api/telemedizin/specializations/{id}` - Fachbereich abrufen
- `PUT /api/telemedizin/specializations/{id}` - Fachbereich aktualisieren
- `DELETE /api/telemedizin/specializations/{id}` - Fachbereich löschen

### Ärzte

- `GET /api/telemedizin/doctors` - Alle Ärzte abrufen
- `POST /api/telemedizin/doctors` - Neuen Arzt erstellen
- `GET /api/telemedizin/doctors/{id}` - Arzt abrufen
- `PUT /api/telemedizin/doctors/{id}` - Arzt aktualisieren
- `DELETE /api/telemedizin/doctors/{id}` - Arzt löschen

### Zeitslots

- `GET /api/telemedizin/timeslots` - Alle verfügbaren Zeitslots abrufen
- `POST /api/telemedizin/timeslots` - Neuen Zeitslot erstellen
- `GET /api/telemedizin/timeslots/{id}` - Zeitslot abrufen
- `PUT /api/telemedizin/timeslots/{id}` - Zeitslot aktualisieren
- `DELETE /api/telemedizin/timeslots/{id}` - Zeitslot löschen
- `GET /api/telemedizin/doctors/{doctor}/timeslots` - Zeitslots für einen Arzt abrufen
- `POST /api/telemedizin/doctors/{doctor}/timeslots/generate` - Zeitslots für einen Arzt generieren

### Termine

- `GET /api/telemedizin/appointments` - Alle Termine abrufen
- `POST /api/telemedizin/appointments` - Neuen Termin erstellen
- `GET /api/telemedizin/appointments/{id}` - Termin abrufen
- `PUT /api/telemedizin/appointments/{id}` - Termin aktualisieren (Status ändern)
- `DELETE /api/telemedizin/appointments/{id}` - Termin löschen
- `GET /api/telemedizin/appointments/patient/{email}` - Termine für einen Patienten abrufen
- `PATCH /api/telemedizin/appointments/{id}/cancel` - Termin stornieren

## Tests

### Tests ausführen

Um die Tests auszuführen, verwenden Sie den folgenden Befehl:

```bash
./vendor/bin/pest
```
