@extends('layouts.app')

@section('content')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <h1 class="ml-1">Deine Projekte</h1>
    @if(auth()->check() && auth()->user()->hasRole('Admin'))
    <form method="GET" action="{{ route('projects.index') }}" class="mb-2 mt-4" id="searchForm">
        <div class="input-group input-group-sm" style="max-width: 400px;">
            <input type="text" name="search" id="searchInput" class="form-control" style="" placeholder="Projekt suchen" value="{{ request()->get('search') }}">
            <div class="input-group-append">
                <button class="btn btn-outline-secondary ml-1" type="submit">Suchen</button>
            </div>
        </div>
    </form>
    @endif
    
    <table class="table ml-1">
        <thead>
            <tr>
                <th data-sort="0">Ort</th>
                @if(auth()->check() && auth()->user()->hasRole('Admin'))
                <th data-sort="1">Benutzer</th>
                @endif
                <th data-sort="2">Postleitzahl</th>
                <th data-sort="3">Wohneinheiten</th>
                <th data-sort="4">Unbesuchte</th>
                <th data-sort="5">Vertrag</th>
                <th data-sort="6">Überleger</th>
                <th data-sort="7">Karte</th>
                <th data-sort="8">Kein Interesse</th>
                <th data-sort="9">Kein Potenzial</th>
                <th data-sort="10">Prozentsatz Vertrag</th>
                <th data-sort="11">Bearbeitungsdatum</th>
                @if(auth()->check() && auth()->user()->hasRole('Admin'))
                    <th>Aktionen</th>
                @endif
            </tr>
        </thead>
        <tbody >
            @php
            $uniqueNames = $projects->unique('ort');
            $perPage = 10; 
            $currentPage = request()->get('page', 1); 
            $slicedProjects = array_slice($uniqueNames->all(), ($currentPage - 1) * $perPage, $perPage);
            $uniqueProjectsPaginated = new \Illuminate\Pagination\LengthAwarePaginator($slicedProjects, count($uniqueNames), $perPage, $currentPage, ['path' => request()->url()]);
            @endphp

            @foreach ($uniqueProjectsPaginated as $project)
            @php
            $users = collect();
            foreach ($projects as $proj) {
                if ($proj->ort == $project->ort && $proj->postleitzahl == $project->postleitzahl) {
                    $users = $users->merge($proj->users);
                }
            }
            $users = $users->unique('id');
        @endphp
                @php
                    $filteredProjects = $projects
                        ->where('ort', $project->ort)
                        ->where('postleitzahl', $project->postleitzahl);

                    $subProjects = collect();
                    foreach ($filteredProjects as $filteredProject) {
                        $subProjects = $subProjects->merge($filteredProject->subProjects);
                    }

                    $groupedSubProjects = $subProjects->groupBy('status');

                    $countUnbesucht = $groupedSubProjects->get('Unbesucht', collect())->count();
                    $countVertrag = $groupedSubProjects->get('Vertrag', collect())->count();
                    $countOverleger = $groupedSubProjects->get('Überleger', collect())->count();
                    $countKarte = $groupedSubProjects->get('Karte', collect())->count();
                    $countKeinInteresse = $groupedSubProjects->get('Kein Interesse', collect())->count();
                    $countKeinPotenzial = $groupedSubProjects->get('Kein Potenzial', collect())->count();
                    
                    // Zählungen für Projekte ohne Subprojekte
                    $countUnbesucht += $filteredProjects->where('status', 'Unbesucht')->count();
                    $countVertrag += $filteredProjects->where('status', 'Vertrag')->count();
                    $countOverleger += $filteredProjects->where('status', 'Überleger')->count();
                    $countKarte += $filteredProjects->where('status', 'Karte')->count();
                    $countKeinInteresse += $filteredProjects->where('status', 'Kein Interesse')->count();
                    $countKeinPotenzial += $filteredProjects->where('status', 'Kein Potenzial')->count();
                    
                    $totalWohneinheiten = $filteredProjects->sum('wohneinheiten');

                    $percentage = ($totalWohneinheiten > 0) ? ($countVertrag / $totalWohneinheiten) * 100 : 0;

                    $lastUpdated = $filteredProjects->max('updated_at');
                @endphp
                <tr>
                    <td><a class="locc" href="{{ route('projects.street', ['ort' => $project->ort, 'postleitzahl' => $project->postleitzahl]) }}" style="text-decoration : none">{{ $project->ort }}</a></td>
                    @if(auth()->check() && auth()->user()->hasRole('Admin'))
                    <td>
                        @foreach ($users as $user)
                            <span>{{ $user->name }}</span><br>
                        @endforeach
                    </td>
                    @endif
                    <td>{{ $project->postleitzahl }}</td>
                    <td>{{ $totalWohneinheiten }}</td>
                    <td>{{ $countUnbesucht }}</td>
                    <td>{{ $countVertrag }}</td>
                    <td>{{ $countOverleger }}</td>
                    <td>{{ $countKarte }}</td>
                    <td>{{ $countKeinInteresse }}</td>
                    <td>{{ $countKeinPotenzial }}</td>
                    <td>{{ number_format($percentage, 2) }}%</td>
                    <td>{{ \Carbon\Carbon::parse($lastUpdated)->isoFormat('DD.MM.YYYY HH:mm') }}</td>
                    @if(auth()->check() && auth()->user()->hasRole('Admin'))
                    <td>
                        <div class="d-flex">
                            <a href="{{ route('projects.export.excel', ['ort' => $project->ort, 'postleitzahl' => $project->postleitzahl]) }}" class="btn btn-sm btn-success mr-2">
                                <i class="fas fa-file-excel"></i>
                            </a>
                            <form id="archiveForm_{{ $project->id }}" action="{{ route('projects.archive', ['ort' => $project->ort, 'postleitzahl' => $project->postleitzahl]) }}" method="post">
                                @csrf
                                @method('DELETE')
                                <button type="button" class="btn btn-sm btn-warning mr-2" onclick="confirmArchive('{{ $project->id }}')">
                                    <i class="fa-solid fa-box-archive"></i>
                                </button>
                            </form>
                            <form id="deleteForm_{{ $project->id }}" action="{{ route('projects.destroy', ['ort' => $project->ort, 'postleitzahl' => $project->postleitzahl]) }}" method="post">
                                @csrf
                                @method('DELETE')
                                <button type="button" class="btn btn-sm btn-danger mr-2" onclick="confirmDelete('{{ $project->id }}')">
                                    <i class="fas fa-trash-alt"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                    @endif
                </tr>
            @endforeach
        </tbody>
    </table>
    {{ $uniqueProjectsPaginated->links() }}

    <div class="modal fade" id="confirmationModal" tabindex="-1" role="dialog" aria-labelledby="confirmationModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="confirmationModalLabel">Bestätigung erforderlich</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    Möchten Sie dieses Projekt wirklich löschen?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Abbrechen</button>
                    <button type="button" class="btn btn-danger" onclick="deleteProject()">Löschen</button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="archiveConfirmationModal" tabindex="-1" role="dialog" aria-labelledby="archiveConfirmationModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="archiveConfirmationModalLabel">Bestätigung erforderlich</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    Möchten Sie dieses Projekt wirklich archivieren?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Abbrechen</button>
                    <button type="button" class="btn btn-warning" onclick="archiveProject()">Archivieren</button>
                </div>
            </div>
        </div>
    </div>
    <div id="loadingOverlay" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background-color: rgba(0, 0, 0, 0.5); z-index: 1000;">
        <div id="loadingSpinner" class="text-center" style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%);">
            <i class="fas fa-spinner fa-spin fa-3x" style="color: white;"></i>
            <p style="color: white;">Daten werden geladen...</p>
        </div>
    </div>
    
    <script>
        function showLoadingOverlay() {
            $('#loadingOverlay').show();
        }
    
        function hideLoadingOverlay() {
            $('#loadingOverlay').hide();
        }
    
        $(document).ready(function() {
            $('a').on('click', function() {
                showLoadingOverlay();
            });
    
            $('form').on('submit', function() {
                showLoadingOverlay();
            });
        });
    
        var archiveProjectId;
    
        function confirmArchive(projectId) {
            archiveProjectId = projectId;
            $('#archiveConfirmationModal').modal('show');
        }
    
        function archiveProject() {
            $('#archiveForm_' + archiveProjectId).submit();
            hideLoadingOverlay(); // Verstecke das Overlay, bevor die Aktion abgeschlossen ist
            $('#archiveConfirmationModal').modal('hide'); // Schließe das Archivierungsmodal
            showLoadingOverlay(); // Zeige den Spinner an
        }
    
        var deleteProjectId;
    
        function confirmDelete(projectId) {
            deleteProjectId = projectId;
            $('#confirmationModal').modal('show');
        }
    
        function deleteProject() {
            $('#deleteForm_' + deleteProjectId).submit();
            hideLoadingOverlay(); // Verstecke das Overlay, bevor die Aktion abgeschlossen ist
            $('#confirmationModal').modal('hide'); // Schließe das Löschmodal
            showLoadingOverlay(); // Zeige den Spinner an
        }

        $('#confirmationModal .close, #confirmationModal .btn-secondary').on('click', function () {
            $('#confirmationModal').modal('hide');
        });

        $('#archiveConfirmationModal .close, #archiveConfirmationModal .btn-secondary').on('click', function () {
            $('#archiveConfirmationModal').modal('hide');
        });
    </script>
@endsection
