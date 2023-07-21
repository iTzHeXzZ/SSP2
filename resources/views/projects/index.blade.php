@extends('layouts.app')


@section('content')
    <h1>Deine Projekte</h1>
    
    <table class="table">
        <thead>
            <tr>
                <th>Ort</th>
                <th>Postleitzahl</th>
                <th>Bearbeitungsdatum</th>
            </tr>
        </thead>
        <tbody>
            @php
                $uniqueNames = $projects->unique('postleitzahl');
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