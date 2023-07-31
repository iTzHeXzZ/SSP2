@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">Projekt und Straßen einem Benutzer zuweisen</div>

                    <div class="card-body">
                        <form method="POST" action="{{ route('assign.project') }}">
                            @csrf
                            <div class="form-group">
                                <label for="location_zipcode">Projekt auswählen:</label>
                                <select name="location_zipcode" id="location_zipcode" class="form-control">
                                    @foreach ($projectsByLocationAndZipcode as $group)
                                        @php
                                            // Das erste Projekt in der Gruppe, um auf Ort und Postleitzahl zuzugreifen
                                            $firstProject = $group->first();
                                        @endphp
                                        <option value="{{ $firstProject->ort }}_{{ $firstProject->postleitzahl }}">{{ $firstProject->ort }}, {{ $firstProject->postleitzahl }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="street">Straße auswählen:</label>
                                <select name="street" id="street" class="form-control">
                                    <!-- Die Straßenauswahl wird später dynamisch aktualisiert -->
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="user_id">Benutzer auswählen:</label>
                                <select name="user_id" id="user_id" class="form-control">
                                    @foreach ($users as $user)
                                        <option value="{{ $user->id }}">{{ $user->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <button type="submit" class="btn btn-primary">Projekt und Straße zuweisen</button>
                        </form>
                    </div>
                    <h2>Zugewiesene Projekte:</h2>
                    @foreach ($allUsers as $user)
                        <p><strong>{{ $user->name }}</strong></p>
                        @foreach ($user->projects as $project)
                        <p>{{ $project->ort }}, {{ $project->strasse }}
                            <form action="{{ route('remove.street.from.project') }}" method="post" style="display: inline-block;">
                                @csrf
                                <input type="hidden" name="user_id" value="{{ $user->id }}">
                                <input type="hidden" name="project_id" value="{{ $project->id }}">
                                <button type="submit" class="btn btn-danger btn-sm">Entfernen</button>
                            </form>
                        </p>
                        @endforeach
                    @endforeach
                </div>
            </div>
        </div>
    </div>
@endsection


@section('scripts')
<script>
        document.getElementById('location_zipcode').addEventListener('change', function() {
            // Ausgewählte Ort und Postleitzahl auslesen
            const selectedLocationZipcode = this.value;
            // Ort und Postleitzahl trennen
            const [ort, postleitzahl] = selectedLocationZipcode.split('_');
            // Ausgewählte Ort und Postleitzahl an den Server senden und Straßen für das Projekt abrufen
            fetch(`/get-streets-for-location-zipcode`, {
                method: 'POST', // POST-Anfrage senden
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}', // CSRF-Token für Laravel
                },
                body: JSON.stringify({ ort, postleitzahl }), // Daten als JSON übergeben
            })
            .then(response => response.json())
            .then(data => {
                // Straßenauswahl leeren
                const streetSelect = document.getElementById('street');
                streetSelect.innerHTML = '';
                // Neue Straßenoptionen hinzufügen
                data.forEach(street => {
                    const option = document.createElement('option');
                    option.value = street.id;
                    option.textContent = street.strasse;
                    streetSelect.appendChild(option);
                });
            })
            .catch(error => console.error('Error:', error));
        });

        // Bei der initialen Laden der Seite die Straßen für die erste Ort und Postleitzahl Kombination laden
        document.addEventListener('DOMContentLoaded', function() {
            const selectedLocationZipcode = document.getElementById('location_zipcode').value;
            const [ort, postleitzahl] = selectedLocationZipcode.split('_');
            fetch(`/get-streets-for-location-zipcode`, {
                method: 'POST', // POST-Anfrage senden
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}', // CSRF-Token für Laravel
                },
                body: JSON.stringify({ ort, postleitzahl }), // Daten als JSON übergeben
            })
            .then(response => response.json())
            .then(data => {
                // Straßenauswahl leeren
                const streetSelect = document.getElementById('street');
                streetSelect.innerHTML = '';
                // Neue Straßenoptionen hinzufügen
                data.forEach(street => {
                    const option = document.createElement('option');
                    option.value = street.id;
                    option.textContent = street.strasse;
                    streetSelect.appendChild(option);
                });
            })
            .catch(error => console.error('Error:', error));
        });

</script>
@endsection

