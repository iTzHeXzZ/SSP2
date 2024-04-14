@extends('layouts.app')

@section('content')
<div class="container">
    <div class="card">
        <div class="card-header">
            <h1>Projektanalyse</h1>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('analyse') }}" class="mb-4">
                <div class="row">
                    <div class="col-md-5">
                        <div class="form-group">
                            <label for="start_date">Startdatum:</label>
                            <input type="date" id="start_date" name="start_date" value="{{ request('start_date', now()->startOfMonth()->toDateString()) }}" class="form-control">
                        </div>
                    </div>
                    <div class="col-md-5">
                        <div class="form-group">
                            <label for="end_date">Enddatum:</label>
                            <input type="date" id="end_date" name="end_date" value="{{ request('end_date', now()->endOfMonth()->toDateString()) }}" class="form-control">
                        </div>
                    </div>
                    <div class="col-md-2 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary">Filtern</button>
                    </div>
                </div>
            </form>

            <table class="table">
                <thead>
                    <tr>
                        <th>Benutzer</th>
                        <th>Vertrag</th>
                        <th>Karte</th>
                        <th>Überleger</th>
                        <th>Kein Interesse</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($stats as $userId => $data)
                    <tr>
                        <td>{{ optional($users->find($userId))->name ?? 'Benutzer nicht gefunden' }}</td>
                        <td>{{ $data['Vertrag'] ?? 0 }}</td>
                        <td>{{ $data['Karte'] ?? 0 }}</td>
                        <td>{{ $data['Überleger'] ?? 0 }}</td>
                        <td>{{ $data['Kein Interesse'] ?? 0 }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection


