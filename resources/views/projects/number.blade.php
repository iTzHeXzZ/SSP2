@extends('layouts.app')


@section('content')
<h2>{{$postleitzahl}},{{$ort}},{{ $strasse }} Hausnummer:</h2>

    <table class="table">
        <thead>
            <tr>
                <th>Hausnummer</th>
                <th>Wohneinheiten</th>
                <th>Bestand</th>
                <th>Notiz</th>
                <th>Bearbeitungsdatum</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($projects as $project)
                <tr>
                    <td>{{ $project->hausnummer }}</td>
                    <td>{{ $project->wohneinheiten }}</td>
                    <td>{{ $project->bestand }} </td>
                    <td>
                        <form method="POST" action="{{ route('projects.update', ['ort' => $project->ort, 'postleitzahl' => $project->postleitzahl, 'strasse' => $project->strasse])}}">
                            @csrf
                            <input type="text" name="notiz" value="{{ $project->notiz }}">
                            <button type="submit" class="btn btn-primary">Speichern</button>
                        </form>
                        </td>
                    <td>{{ $project->bearbeitungsdatum }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
@endsection
