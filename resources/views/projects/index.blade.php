@extends('layouts.app')


@section('content')
    <h1>Deine Projekte</h1>
    
    <table class="table">
        <thead>
            <tr>
                <th data-sort="0">Ort</th>
                <th data-sort="1">Postleitzahl</th>
                <th data-sort="2">Bearbeitungsdatum</th>
            </tr>
        </thead>
        <tbody>
            @php
                $uniqueNames = $projects->unique('ort');
            @endphp
            @foreach ($uniqueNames as $project)
                <tr>
                    <td><a class="locc" href="{{ route('projects.street', ['ort' => $project->ort, 'postleitzahl' => $project->postleitzahl]) }}" style="text-decoration : none">{{ $project->ort }} </a></td>
                    <td>{{ $project->postleitzahl }}</td>
                    <td>{{ $project->updated_at }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
@endsection