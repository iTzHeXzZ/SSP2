@extends('layouts.app')

@section('content')
    <h1>Straßen in {{$postleitzahl}}, {{$ort}}</h1>

    <table class="table">
        <thead>
            <tr>
                <th data-sort="0">Strasse</th>
                @if(auth()->check() && auth()->user()->hasRole('Admin'))
                <th data-sort="1">Benutzer</th>
                @endif
                <th data-sort="2">Wohneinheiten</th>
                <th data-sort="3">Bestand</th>
                <th data-sort="4">Unbesuchte</th>
                <th data-sort="5">Vertrag</th>
                <th data-sort="6">Überleger</th>
                <th data-sort="7">Karte</th>
                <th data-sort="8">Fremd VP</th>
                <th data-sort="9">Kein Interesse</th>
                <th data-sort="10">Kein Potenzial</th>
                <th data-sort="11">Prozentsatz Vertrag</th> 
                <th data-sort="12">Bearbeitungsdatum</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($gwohneinheiten as $project)
                @if (auth()->user()->hasRole(['Admin', 'Viewer']) || auth()->user()->projects->contains($project))
                    <tr>
                        <td><a class="locc" href="{{ route('projects.number', ['ort' => $project->ort, 'postleitzahl' => $project->postleitzahl, 'strasse' => $project->strasse]) }}" style="text-decoration: none">{{ $project->strasse }}</a></td>
                        
                        @if(auth()->check() && auth()->user()->hasRole('Admin'))
                        <td>
                            @foreach ($project->users as $user)
                                <span>{{ $user->name }}</span><br>
                            @endforeach
                        </td>
                        @endif
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
                            @if ($project->wohneinheiten > 0)
                                @php
                                    $denominator = $project->wohneinheiten - $countKeinPotenzial[$project->strasse];
                                    $percentage = ($denominator > 0) ? (($countVertrag[$project->strasse] + $countFremdVP[$project->strasse]) / $denominator) * 100 : 0;
                                @endphp
                                {{ number_format($percentage, 2) }}%
                            @else
                                0%
                            @endif
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
