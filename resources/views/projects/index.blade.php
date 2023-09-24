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
            @endphp
            @foreach ($uniqueNames as $project)
                @php
                    $countUnbesucht = $projects->where('ort', $project->ort)
                        ->where('postleitzahl', $project->postleitzahl)
                        ->where('status', 'Unbesucht')->count();
                    
                    $countVertrag = $projects->where('ort', $project->ort)
                        ->where('postleitzahl', $project->postleitzahl)
                        ->where('status', 'Vertrag')->count();
                    
                    $countOverleger = $projects->where('ort', $project->ort)
                        ->where('postleitzahl', $project->postleitzahl)
                        ->where('status', 'Überleger')->count();
                    
                    $countKarte = $projects->where('ort', $project->ort)
                        ->where('postleitzahl', $project->postleitzahl)
                        ->where('status', 'Karte')->count();
                    
                    $countKeinInteresse = $projects->where('ort', $project->ort)
                        ->where('postleitzahl', $project->postleitzahl)
                        ->where('status', 'Kein Interesse')->count();
                    
                    $totalWohneinheiten = $projects->where('ort', $project->ort)
                        ->where('postleitzahl', $project->postleitzahl)
                        ->sum('wohneinheiten');
                    
                    $percentage = 0;
                    if ($totalWohneinheiten > 0) {
                        $percentage = ($countVertrag / $totalWohneinheiten) * 100;
                    }

                    $lastUpdated = $projects
                        ->where('ort', $project->ort)
                        ->where('postleitzahl', $project->postleitzahl)
                        ->max('updated_at');
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
@endsection
