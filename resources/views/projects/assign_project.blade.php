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
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">Zugewiesene Projekte und Straßen</div>

<div class="card-body">
    <ul>
        @foreach ($allUsers as $user)
            <li>
                <strong>{{ $user->name }}</strong>:
                <button class="toggle-streets-btn" data-toggle="modal" data-target="#assignStreetsModal" data-user-id="{{ $user->id }}">
                    <i class="fas fa-chevron-down"></i> Anzeigen
                </button>
            </li>
        @endforeach
    </ul>
</div>
      
        </div>
    </div>
</div>
<!-- Modal -->
<div class="modal fade" id="assignStreetsModal" tabindex="-1" role="dialog" aria-labelledby="assignStreetsModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="assignStreetsModalLabel">Straßen löschen</h5>
                {{-- <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button> --}}
            </div>
            <div class="modal-body">
            </div>
            {{-- <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Schließen</button>
            </div> --}}
        </div>
    </div>
</div>


@endsection

@section('scripts')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-multiselect/0.9.15/js/bootstrap-multiselect.min.js"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-multiselect/0.9.15/css/bootstrap-multiselect.css">
<script>
   $(document).ready(function() {
    const locationZipcodeSelect = $('#location_zipcode');
    const streetSelect = $('#street');
    const projectIdInput = $('#project_id');

    locationZipcodeSelect.on('change', function() {

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
            streetSelect.multiselect('rebuild');
        })
        .catch(error => console.error('Error:', error));
    });

    $('.toggle-streets-btn').click(function() {
    const userId = $(this).data('user-id');

    fetch(`/get-streets-for-user/${userId}`)
        .then(response => response.json())
        .then(data => {
            const modalBody = $('#assignStreetsModal .modal-body');
            const projectName = data.projectName;

            // Orte eindeutig machen
            const uniqueOrte = Array.from(new Set(data.streetsAndOrte.map(item => item.ort)));

            modalBody.html(`
                <form action="{{ route('remove.street.from.project') }}" method="post" style="display: inline-block;">
                    @csrf
                    <input type="hidden" name="user_id" value="${userId}">
                    <input type="hidden" name="project_id" value="${projectName}">
                    
                    ${uniqueOrte.map(ort => {
                        const uniqueStreetsForOrt = Array.from(new Set(
                            data.streetsAndOrte
                                .filter(item => item.ort === ort)
                                .map(item => `${item.strasse}, ${item.ort}`)
                        ));

                        return `
                            <ul class="user-streets-list-${userId}" style="list-style-type: none;">
                                <li>
                                    <h5>${ort}</h5>
                                </li>
                                ${uniqueStreetsForOrt.map(streetAndOrt => `
                                    <li>
                                        <label>
                                            <input type="checkbox" name="streets[]" value="${streetAndOrt}" class="street-checkbox">
                                            <strong>${streetAndOrt}</strong>
                                        </label>
                                    </li>
                                `).join('')}
                            </ul>
                        `;
                    }).join('')}

                    <button type="submit" class="btn btn-danger btn-sm">Ausgewählte Straßen entfernen</button>
                </form>
            `);

            $('#assignStreetsModal').modal('show');
        })
        .catch(error => console.error('Error:', error));
});

});

</script>
@endsection



