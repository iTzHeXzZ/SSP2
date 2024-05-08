@extends('layouts.app')

@section('content')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <h1>Archivierte Projekte</h1>
    
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
                <th data-sort="11">Aktionen</th> 
            </tr>
        </thead>
        <tbody>
            @foreach ($archivedProjects->unique('ort', 'postleitzahl') as $project)
                @php
                    $projectData = $archivedProjects->where('ort', $project->ort)
                        ->where('postleitzahl', $project->postleitzahl);
                    
                    $countUnbesucht = $projectData->where('status', 'Unbesucht')->count();
                    $countVertrag = $projectData->where('status', 'Vertrag')->count();
                    $countOverleger = $projectData->where('status', 'Überleger')->count();
                    $countKarte = $projectData->where('status', 'Karte')->count();
                    $countKeinInteresse = $projectData->where('status', 'Kein Interesse')->count();
                    $countKeinPotenzial = $projectData->where('status', 'Kein Potenzial')->count();
                    $totalWohneinheiten = $projectData->sum('wohneinheiten');
                    $percentage = $totalWohneinheiten > 0 ? ($countVertrag / $totalWohneinheiten) * 100 : 0;
                    $lastUpdated = $projectData->max('updated_at');
                @endphp
                <tr>
                    <td>{{ $project->ort }}</td>
                    <td>{{ $project->postleitzahl }}</td>
                    <td>{{ $totalWohneinheiten }}</td>
                    <td>{{ $countUnbesucht }}</td>
                    <td>{{ $countVertrag }}</td>
                    <td>{{ $countOverleger }}</td>
                    <td>{{ $countKarte }}</td>
                    <td>{{ $countKeinInteresse }}</td>
                    <td>{{ $countKeinPotenzial }}</td>
                    <td>{{ number_format($percentage, 2) }}%</td>
                    <td>{{ $lastUpdated }}</td>
                    <td>
                        <form action="{{ route('projects.restore', ['ort' => $project->ort, 'postleitzahl' => $project->postleitzahl]) }}" method="post">
                            @csrf
                            @method('PUT')
                            <button type="submit" class="btn btn-success"><i class="fa-regular fa-folder-open"></i></button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
@endsection
