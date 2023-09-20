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
                            <select name="project_id" id="location_zipcode" class="form-control">
                                @foreach ($projectsByLocationAndZipcode as $group)
                                    @php
                                        $firstProject = $group->first();
                                    @endphp
                                    <option value="{{ $firstProject->id }}_{{ $firstProject->ort }}_{{ $firstProject->postleitzahl }}">{{ $firstProject->ort }}, {{ $firstProject->postleitzahl }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="street">Straße auswählen:</label>
                            <select name="street" id="street" class="form-control">
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
            </div>
        </div>
    </div>
</div>
<div class="row mt-4">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">Zugewiesene Projekte und Straßen</div>

            <div class="card-body">
                <ul>
                    @foreach ($allUsers as $user)
                        <li>
                            <strong>{{ $user->name }}</strong>:
                            <ul>
                                @php
                                    $displayedStreets = []; // Array, um bereits angezeigte Straßen zu verfolgen
                                @endphp
            
                                @foreach ($user->projects as $project)
                                    @if (!in_array($project->strasse, $displayedStreets))
                                        <li>
                                            {{ $project->ort }}, {{ $project->strasse }}
                                            <form action="{{ route('remove.street.from.project') }}" method="post" style="display: inline-block;">
                                                @csrf
                                                <input type="hidden" name="user_id" value="{{ $user->id }}">
                                                <input type="hidden" name="strasse" value="{{ $project->strasse }}">
                                                <button type="submit" class="btn btn-danger btn-sm">Entfernen</button>
                                            </form>
                                        </li>
                                        @php
                                            $displayedStreets[] = $project->strasse; // Straße zur Liste hinzufügen
                                        @endphp
                                    @endif
                                @endforeach
                            </ul>
                        </li>
                    @endforeach
                </ul>
            </div>            
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const locationZipcodeSelect = document.getElementById('location_zipcode');
        const streetSelect = document.getElementById('street');
    
        locationZipcodeSelect.addEventListener('change', function() {
            // Lösche alle vorherigen Optionen aus dem Straßen-Dropdown
            streetSelect.innerHTML = '';
    
            const selectedLocationZipcode = this.value;
            const [projectId, ort, postleitzahl] = selectedLocationZipcode.split('_');
    
            fetch(`/get-streets-for-location-zipcode/${ort}/${postleitzahl}`, {
                method: 'GET',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                },
            })
            .then(response => response.json())
            .then(data => {
                // Objekt zur Gruppierung von Straßen nach Namen
                const groupedStreets = {};
    
                data.forEach(street => {
                    if (!groupedStreets[street.strasse]) {
                        groupedStreets[street.strasse] = [];
                    }
                    groupedStreets[street.strasse].push(street.hausnummer);
                });
    
                // Neue Straßenoptionen hinzufügen
                Object.keys(groupedStreets).forEach(strasse => {
                    const option = document.createElement('option');
                    option.value = strasse;
                    option.textContent = strasse;
                    streetSelect.appendChild(option);
                });
            })
            .catch(error => console.error('Error:', error));
        });
    });
    </script>
    
@endsection
