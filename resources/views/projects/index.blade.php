@extends('layouts.app')

@section('content')
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
                <th data-sort="8">Prozentsatz Vertrag</th>
                <th data-sort="9">Bearbeitungsdatum</th>
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

                    $countUnbesucht = $filteredProjects->where('status', 'Unbesucht')->count();
                    $countVertrag = $filteredProjects->where('status', 'Vertrag')->count();
                    $countOverleger = $filteredProjects->where('status', 'Überleger')->count();
                    $countKarte = $filteredProjects->where('status', 'Karte')->count();
                    $countKeinInteresse = $filteredProjects->where('status', 'Kein Interesse')->count();
                    
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
                    <td>{{ number_format($percentage, 2) }}%</td>
                    <td>{{ $lastUpdated  }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
    {{ $uniqueProjectsPaginated->links() }}
@endsection
