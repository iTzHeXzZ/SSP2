@extends('layouts.app')

@section('content')
    <h1>Neues Projekt erstellen</h1>

    <form method="POST" action="{{ route('projects.store') }}">
        @csrf

        <div class="form-group">
            <label for="ort">Ort:</label>
            <input type="text" name="ort" id="ort" class="form-control" required>
        </div>

        <div class="form-group">
            <label for="postleitzahl">Postleitzahl:</label>
            <input type="text" name="postleitzahl" id="postleitzahl" class="form-control" required>
        </div>

        <div class="form-group">
            <label for="wohneinheiten">Wohneinheiten:</label>
            <input type="number" name="wohneinheiten" id="wohneinheiten" class="form-control" required>
        </div>

        <div class="form-group">
            <label for="bearbeitungsdatum">Bearbeitungsdatum:</label>
            <input type="date" name="bearbeitungsdatum" id="bearbeitungsdatum" class="form-control" required>
        </div>

        <button type="submit" class="btn btn-primary">Projekt speichern</button>
    </form>
@endsection
