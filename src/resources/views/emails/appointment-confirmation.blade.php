@component('mail::message')

# Terminbestätigung

Sehr geehrte(r) {{ $patientName }},

wir bestätigen hiermit Ihren Termin bei **{{ $doctorName }}** am **{{ $dateTime }}**. \n

## Termindetails

- **Arzt:** {{ $doctorName }}
- **Fachbereich:** {{ $specialization }}
- **Datum & Uhrzeit:** {{ $dateTime }}

@component('mail::button', ['url' => 'http://localhost:3000'])
Meine Termine verwalten
@endcomponent


@endcomponent