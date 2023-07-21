@extends('layouts.app')


@section('content')
    <h1>Straßen in {{$postleitzahl}},{{$ort}}</h1>

    <table class="table">
        <thead>
            <tr>
                <th>Strasse</th>
                <th>Wohneinheiten</th>
                <th>Bestand</th>
                <th>Bearbeitungsdatum</th>
            </tr>
        </thead>
        <tbody>
            @php
                $uniqueNames = $projects->unique('strasse');
            @endphp
            @foreach ($uniqueNames as $project)
                <tr>
                    <td><a class="locc" href="{{ route('projects.number', ['ort' => $project->ort, 'postleitzahl' => $project->postleitzahl, 'strasse' => $project->strasse]) }}" style="text-decoration : none">{{ $project->strasse }} </a></td>
                    <td>{{ $project->wohneinheiten }}</td>
                    <td>{{ $project->bestand }}</td>
                    <td>{{ $project->updated_at }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
@endsection
