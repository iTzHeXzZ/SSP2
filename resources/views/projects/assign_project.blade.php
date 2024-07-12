@extends('layouts.app')

@section('content')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
<div class="container">
    @if(session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
    @endif
    <div class="row">
        <div class="col-md-6">
            <div class="card mb-4">
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
                        <div class="form-group" id="selectedStreetsList" style="display: none;">
                            <label class="font-weight-bold">Ausgewählte Straßen:</label>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="card">
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col-md-4">
                                                    <ul id="selectedStreetsUl1" class="list-unstyled"></ul>
                                                </div>
                                                <div class="col-md-4">
                                                    <ul id="selectedStreetsUl2" class="list-unstyled"></ul>
                                                </div>
                                                <div class="col-md-4">
                                                    <ul id="selectedStreetsUl3" class="list-unstyled"></ul>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
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
                        <input type="hidden" name="streets[]" id="selected_streets">
                        <button type="submit" class="btn btn-primary">Projekt und Straße zuweisen</button>
                    </form>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card mb-4">
                <div class="card-header">Zugewiesene Projekte und Straßen</div>
                <div class="card-body">
                    <ul class="list-group">
                        @php
                            $perPage = 10;
                            $page = request()->get('page', 1);
                            $paginatedUsers = $allUsers->slice(($page - 1) * $perPage, $perPage);
                            $lastPage = ceil($allUsers->count() / $perPage);
                        @endphp
                        @foreach ($paginatedUsers as $user)
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <strong>{{ $user->name }}</strong>
                                <button class="btn btn-link p-0 toggle-streets-btn" data-toggle="modal" data-target="#assignStreetsModal" data-user-id="{{ $user->id }}">
                                    <i class="fas fa-chevron-down"></i>
                                </button>
                            </li>
                        @endforeach
                    </ul>
                </div>
                <div class="card-footer">
                    <nav aria-label="Users Pagination">
                        <ul class="pagination justify-content-end">
                            <li class="page-item {{ $page <= 1 ? 'disabled' : '' }}">
                                <a class="page-link" href="{{ $page <= 1 ? '#' : '?page=1' }}" tabindex="-1" aria-disabled="true"><<</a>
                            </li>
                            <li class="page-item {{ $page <= 1 ? 'disabled' : '' }}">
                                <a class="page-link" href="{{ $page <= 1 ? '#' : '?page=' . ($page - 1) }}" tabindex="-1" aria-disabled="true">Zurück</a>
                            </li>
                            @for ($i = max(1, $page - 2); $i <= min($page + 2, $lastPage); $i++)
                                <li class="page-item {{ $page == $i ? 'active' : '' }}">
                                    <a class="page-link" href="?page={{ $i }}">{{ $i }}</a>
                                </li>
                            @endfor
                            <li class="page-item {{ $page >= $lastPage ? 'disabled' : '' }}">
                                <a class="page-link" href="?page={{ $page + 1 }}">Weiter</a>
                            </li>
                            <li class="page-item {{ $page >= $lastPage ? 'disabled' : '' }}">
                                <a class="page-link" href="?page={{ $lastPage }}">>></a>
                            </li>
                        </ul>
                    </nav>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Street Selection Modal -->
<div class="modal fade" id="selectStreetModal" tabindex="-1" role="dialog" aria-labelledby="selectStreetModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-scrollable" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="selectStreetModalLabel">Straßen auswählen</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div id="streetsContainer"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary mr-auto" id="selectAllStreetsBtn">Alle auswählen</button>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Schließen</button>
                <button type="button" class="btn btn-primary " id="saveSelectedStreetsBtn">Speichern</button>
            </div>
        </div>
    </div>
</div>


<div class="modal fade" id="assignStreetsModal" tabindex="-1" role="dialog" aria-labelledby="assignStreetsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-scrollable" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="assignStreetsModalLabel">Straßen verwalten</h5>
                <button type="button" class="btn btn-danger btn-sm ml-auto" id="removeAllStreetsBtn">Alle Straßen entfernen</button>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" style="max-height: calc(100vh - 200px); overflow-y: auto;">
            </div>
            <div class="modal-footer justify-content-between">
                <button type="button" class="btn btn-danger btn-sm" id="removeSelectedStreetsBtn">Ausgewählte Straßen entfernen</button>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Schließen</button>
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>
<script>
    $(document).ready(function () {
        const locationZipcodeSelect = $('#location_zipcode');
        const streetsContainer = $('#streetsContainer');
        const projectIdInput = $('#project_id');
        const selectedStreetsInput = $('#selected_streets');
        const selectStreetModalLabel = $('#selectStreetModalLabel');
        const selectAllStreetsBtn = $('#selectAllStreetsBtn');

        locationZipcodeSelect.on('change', function () {
            streetsContainer.empty();

            const selectedLocationZipcode = $(this).val();
            const [projectId, ort, postleitzahl] = selectedLocationZipcode.split('_');
            projectIdInput.val(projectId);

            // Update modal title with the selected project
            selectStreetModalLabel.text(`Straßen auswählen für ${ort}, ${postleitzahl}`);

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

                    const sortedStreets = Object.entries(groupedStreets).sort(([a], [b]) => a.localeCompare(b));

                    sortedStreets.forEach(([strasse, hausnummern]) => {
                        const streetHtml = `
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" value="${strasse}" id="street_${strasse}">
                                <label class="form-check-label" for="street_${strasse}">${strasse}</label>
                            </div>
                        `;
                        streetsContainer.append(streetHtml);
                    });

                    $('#selectStreetModal').modal('show');
                })
                .catch(error => console.error('Error:', error));
        });

        selectAllStreetsBtn.on('click', function () {
            streetsContainer.find('input[type="checkbox"]').prop('checked', true);
        });

        $('#saveSelectedStreetsBtn').on('click', function () {
            const selectedStreets = [];
            $('#streetsContainer').find('input[type="checkbox"]:checked').each(function () {
                selectedStreets.push($(this).val());
            });

            $('#selected_streets').val(JSON.stringify(selectedStreets));

            $('#selectedStreetsUl1').empty();
            $('#selectedStreetsUl2').empty();
            $('#selectedStreetsUl3').empty();

            const column1 = $('#selectedStreetsUl1');
            const column2 = $('#selectedStreetsUl2');
            const column3 = $('#selectedStreetsUl3');

            selectedStreets.forEach((street, index) => {
                const listItem = `<li>${street}</li>`;
                if (index % 3 === 0) {
                    column1.append(listItem);
                } else if (index % 3 === 1) {
                    column2.append(listItem);
                } else {
                    column3.append(listItem);
                }
            });

            $('#selectedStreetsList').show();

            $('#selectStreetModal').modal('hide');
        });





        $('.toggle-streets-btn').click(function () {
            const userId = $(this).data('user-id');

            fetch(`/get-streets-for-user/${userId}`)
                .then(response => response.json())
                .then(data => {
                    const modalBody = $('#assignStreetsModal .modal-body');
                    const projectName = data.projectName;

                    const uniqueOrte = Array.from(new Set(data.streetsAndOrte.map(item => item.ort)));

                    modalBody.html(`
                        <form id="removeAllStreetsForm" action="{{ route('remove.all.streets') }}" method="POST">
                            @csrf
                            <input type="hidden" name="user_id" value="${userId}">
                            <input type="hidden" name="project_id" value="${projectName}">
                        </form>
                        
                        <form id="removeSelectedStreetsForm" action="{{ route('remove.street.from.project') }}" method="POST">
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
                        </form>
                    `);

                    $('#assignStreetsModal').modal('show');
                })
                .catch(error => console.error('Error:', error));
        });

        $('#removeAllStreetsBtn').click(function () {
            if (confirm('Möchten Sie wirklich alle Straßen entfernen?')) {
                $('#removeAllStreetsForm').submit();
            }
        });

        $('#removeSelectedStreetsBtn').click(function () {
            if (confirm('Möchten Sie die ausgewählten Straßen entfernen?')) {
                $('#removeSelectedStreetsForm').submit();
            }
        });


        $('#assignStreetsModal .close, #assignStreetsModal .btn-secondary').on('click', function () {
            $('#assignStreetsModal').modal('hide');
        });

        $('#selectStreetModal .close, #selectStreetModal .btn-secondary').on('click', function () {
            $('#selectStreetModal').modal('hide');
        });

        $('#assignStreetsModal').on('hidden.bs.modal', function () {
            $(this).find('.modal-body').empty();
        });

    });
</script>
@endsection
