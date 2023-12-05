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
                                <option value="" disabled selected>Bitte wählen Sie ein Projekt</option>
                                @foreach ($projectsByLocationAndZipcode as $group)
                                    @php
                                        $firstProject = $group->first();
                                    @endphp
                                        <option value="{{ $firstProject->id }}_{{ $firstProject->ort }}_{{ $firstProject->postleitzahl }}" {{ old('project_id') == $firstProject->id ? 'selected' : '' }}>
                                                        {{ $firstProject->ort }}, {{ $firstProject->postleitzahl }}
                                        </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="street">Straßen auswählen:</label>
                            <select name="streets[]" id="street" class="form-control" multiple>
                                <!-- Hier werden die Straßenoptionen eingefügt -->
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
                        <input type="hidden" name="project_id" id="project_id" value="{{ old('project_id') }}">
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
                            <button class="toggle-streets-btn" data-toggle-target=".user-streets-list-{{ $user->id }}">
                                <i class="fas fa-chevron-down"></i> Anzeigen
                            </button>
                            <ul class="user-streets-list-{{ $user->id }}" style="display: none;">
                                @php
                                    $displayedStreets = [];
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
                                            $displayedStreets[] = $project->strasse;
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
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-multiselect/0.9.15/js/bootstrap-multiselect.min.js"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-multiselect/0.9.15/css/bootstrap-multiselect.css">
<script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script> <!-- Font Awesome-Icon-Set -->
<script>
   $(document).ready(function() {
    const locationZipcodeSelect = $('#location_zipcode');
    const streetSelect = $('#street');
    const projectIdInput = $('#project_id'); // Hier die ID des Input-Felds für project_id

    locationZipcodeSelect.change(function() {
        // Lösche alle vorherigen Optionen aus dem Straßen-Dropdown
        streetSelect.empty();

        const selectedLocationZipcode = $(this).val();
        const [projectId, ort, postleitzahl] = selectedLocationZipcode.split('_');
        projectIdInput.val(projectId);
        console.log('Projekt-ID:', projectId);

        fetch(`/get-streets-for-location-zipcode/${ort}/${postleitzahl}`, {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
            },
        })
        .then(response => response.json())
        .then(data => {
            const groupedStreets = {};

            data.forEach(street => {
                if (!groupedStreets[street.strasse]) {
                    groupedStreets[street.strasse] = [];
                }
                groupedStreets[street.strasse].push(street.hausnummer);
            });

            $.each(groupedStreets, function(strasse, hausnummern) {
                streetSelect.append($('<option>', {
                    value: strasse,
                    text: strasse
                }));
            });

            streetSelect.multiselect({
                enableFiltering: true,
                maxHeight: 300,
            });
        })
        .catch(error => console.error('Error:', error));
    });

    // Toggle-Funktion für die Straßenliste
    $('.toggle-streets-btn').click(function() {
        const targetSelector = $(this).data('toggle-target');
        $(targetSelector).toggle();
        const icon = $(this).find('i');
        if ($(targetSelector).is(':visible')) {
            icon.removeClass('fa-chevron-down').addClass('fa-chevron-up');
            $(this).text('Verbergen');
        } else {
            icon.removeClass('fa-chevron-up').addClass('fa-chevron-down');
            $(this).text('Anzeigen');
        }
    });
});

</script>
@endsection



