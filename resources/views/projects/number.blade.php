@extends('layouts.app')


@section('content')
<h2>{{$postleitzahl}},{{$ort}},{{ $strasse }} Hausnummer:</h2>

    <table class="table">
        <thead>
            <tr>
                <th>Hausnummer</th>
                <th>Wohneinheiten</th>
                <th>Bestand</th>
                <th>Status</th>
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
                        <form method="POST" action="{{ route('projects.update', $project->id)}}">
                         @csrf
                         <select name="status">
                            @foreach ($statusOptions as $option)
                                <option value="{{ $option }}" {{ $project->status === $option ? 'selected' : '' }}>
                                    {{ $option }}
                                </option>
                            @endforeach
                        </select>
                        <button type="submit" class="btn btn-primary">Speichern</button>
                        </form>
                    </td>
                    <td>
                        <form method="POST" action="{{ route('projects.update', $project->id)}}">
                            @csrf
                            <input type="text" name="notiz" value="{{ $project->notiz }}">
                            <button type="submit" class="btn btn-primary">Speichern</button>
                        </form>
                        </td>
                    <td>{{ $project->updated_at }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
@endsection
