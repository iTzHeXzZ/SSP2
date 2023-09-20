@extends('layouts.app')

@section('content')
    <h1>Straßen in {{$postleitzahl}}, {{$ort}}</h1>

    <table class="table">
        <thead>
            <tr>
                <th data-sort="0">Strasse</th>
                <th data-sort="1">Wohneinheiten</th>
                <th data-sort="2">Bestand</th>
                <th data-sort="3">Bearbeitungsdatum</th>
            </tr>
        </thead>
        <tbody>
            @php
                $uniqueNames = $projects->unique('strasse');
            @endphp
            @foreach ($uniqueNames as $project)
                @if (auth()->user()->hasRole('Admin') || auth()->user()->projects->contains($project))
                    <tr>
                        <td><a class="locc" href="{{ route('projects.number', ['ort' => $project->ort, 'postleitzahl' => $project->postleitzahl, 'strasse' => $project->strasse]) }}" style="text-decoration: none">{{ $project->strasse }}</a></td>
                        <td>{{ $project->wohneinheiten }}</td>
                        <td>{{ $project->bestand }}</td>
                        <td>{{ $project->updated_at }}</td>
                    </tr>
                @endif
            @endforeach
        </tbody>
    </table>
@endsection


