@extends('layouts.app')

@section('content')
    <h1>Statusauswertung</h1>

    <form method="GET" action="{{ route('status.index') }}">
        @csrf
        <label for="user_id">Benutzer auswählen:</label>
        <select name="user_id" id="user_id">
            @foreach ($users as $user)
                <option value="{{ $user->id }}" {{ $selectedUser && $selectedUser->id == $user->id ? 'selected' : '' }}>
                    {{ $user->name }}
                </option>
            @endforeach
        </select>
        <button type="submit">Projekte anzeigen</button>
    </form>

    @if ($groupedProjects ?? false)
        <h2>Projekte für: {{ $selectedUser->name }}</h2>
        <ul>
            @foreach ($groupedProjects as $location => $projects)
                <li>
                    <a href="{{ route('status.showLocation', ['user_id' => $selectedUser->id, 'location' => $location]) }}">
                        {{ $location }}
                    </a>
                </li>
            @endforeach
        </ul>
    @endif
@endsection



