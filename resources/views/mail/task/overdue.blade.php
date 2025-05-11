<x-mail::message>
Hallo {{ $name }},

wir möchten Sie daran erinnern, dass die folgende Aufgabe leider noch nicht abgeschlossen wurde und inzwischen überfällig ist:

Aufgabe: **{{ $task['title'] }}**

Fälligkeit: **{{ $task['deadline'] }}**

Bitte erledigen Sie die Aufgabe zeitnah oder geben Sie uns eine kurze Rückmeldung.

Vielen Dank für Ihre Mithilfe!

Freundliche Grüße,<br>
{{ config('app.name') }}
</x-mail::message>
