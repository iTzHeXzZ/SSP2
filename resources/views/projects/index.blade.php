@extends('layouts.app')

@section('content')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <h1>Deine Projekte</h1>
    
    <table class="table">
        <thead>
            <tr>
                <th data-sort="0">Ort</th>
                <th data-sort="1">Postleitzahl</th>
                <th data-sort="2">Wohneinheiten</th>
                <th data-sort="3">Unbesuchte</th>
                <th data-sort="4">Vertrag</th>
                <th data-sort="5">Überleger</th>
                <th data-sort="6">Karte</th>
                <th data-sort="7">Kein Interesse</th>
                <th data-sort="8">Kein Potenzial</th>
                <th data-sort="9">Prozentsatz Vertrag</th>
                <th data-sort="10">Bearbeitungsdatum</th>
                @if(auth()->check() && auth()->user()->hasRole('Admin'))
                    <th>Aktionen</th>
                @endif
            </tr>
        </thead>
        <tbody>
            @php
            $uniqueNames = $projects->unique('ort');
            $perPage = 10; 
            $currentPage = request()->get('page', 1); 
            $slicedProjects = array_slice($uniqueNames->all(), ($currentPage - 1) * $perPage, $perPage);
            $uniqueProjectsPaginated = new \Illuminate\Pagination\LengthAwarePaginator($slicedProjects, count($uniqueNames), $perPage, $currentPage, ['path' => request()->url()]);
            @endphp

            @foreach ($uniqueProjectsPaginated as $project)
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
                        <form id="deleteForm_{{ $project->id }}" action="{{ route('projects.destroy', ['ort' => $project->ort, 'postleitzahl' => $project->postleitzahl]) }}" method="post">
                            @csrf
                            @method('DELETE')
                            <button type="button" class="btn btn-danger" onclick="confirmDelete('{{ $project->id }}')"><i class="fas fa-trash-alt"></i></button>
                        </form>
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

    <script>
        var deleteProjectId;

        function confirmDelete(projectId) {
            deleteProjectId = projectId;
            $('#confirmationModal').modal('show');
        }

        function deleteProject() {
            $('#deleteForm_' + deleteProjectId).submit();
        }
    </script>
@endsection
