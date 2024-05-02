@extends('layouts.app')

@section('content')
    <h1>Straßen in {{$postleitzahl}}, {{$ort}}</h1>

    <table class="table">
        <thead>
            <tr>
                <th data-sort="0">Strasse</th>
                <th data-sort="1">Wohneinheiten</th>
                <th data-sort="2">Bestand</th>
                <th data-sort="3">Unbesuchte</th>
                <th data-sort="4">Vertrag</th>
                <th data-sort="5">Überleger</th>
                <th data-sort="6">Karte</th>
                <th data-sort="7">Fremd VP</th>
                <th data-sort="8">Kein Interesse</th>
                <th data-sort="8">Kein Potenzial</th>
                <th data-sort="9">Prozentsatz Vertrag</th> 
                <th data-sort="10">Bearbeitungsdatum</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($gwohneinheiten as $project)
                @if (auth()->user()->hasRole(['Admin', 'Viewer']) || auth()->user()->projects->contains($project))
                    <tr>
                        <td><a class="locc" href="{{ route('projects.number', ['ort' => $project->ort, 'postleitzahl' => $project->postleitzahl, 'strasse' => $project->strasse]) }}" style="text-decoration: none">{{ $project->strasse }}</a></td>
                        <td>{{ $project->wohneinheiten }}</td>
                        <td>{{ $project->bestand }}</td>
                        <td>{{ $countUnbesucht[$project->strasse] }}</td>
                        <td>{{ $countVertrag[$project->strasse] }}</td>
                        <td>{{ $countOverleger[$project->strasse] }}</td>
                        <td>{{ $countKarte[$project->strasse] }}</td>
                        <td>{{ $countFremdVP[$project->strasse] }}</td>
                        <td>{{ $countKeinInteresse[$project->strasse] }}</td>
                        <td>{{ $countKeinPotenzial[$project->strasse] }}</td>
                        <td>
                            @php
                                $percentage = 0;
                                if ($project->wohneinheiten > 0) {
                                    $percentage = (($countVertrag[$project->strasse] + $countFremdVP[$project->strasse]) / ($project->wohneinheiten - $countKeinPotenzial[$project->strasse])) * 100;
                                }
                            @endphp
                            {{ number_format($percentage, 2) }}%
                        </td>
                        <td>
                            @php
                                $lastUpdated = $projects
                                    ->where('ort', $project->ort)
                                    ->where('postleitzahl', $project->postleitzahl)
                                    ->where('strasse', $project->strasse)
                                    ->max('updated_at');
                            @endphp
                            {{ \Carbon\Carbon::parse($lastUpdated)->isoFormat('DD.MM.YYYY HH:mm') }}
                        </td>
                    </tr>
                @endif
            @endforeach
        </tbody>
    </table>
@endsection
