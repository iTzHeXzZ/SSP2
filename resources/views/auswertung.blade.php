@extends('layouts.app')

@section('content')
<div class="container">
    <div class="card mt-4">
        <div class="card-header">
            <h2>Benutzer und Aufträge</h2>
        </div>
        <div class="card-body">
            <table class="table">
                <thead>
                    <tr>
                        <th>Benutzer</th>
                        @foreach ($customColumnNames as $columnName)
                            <th>{{ $columnName }}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @foreach ($users as $user)
                        <tr>
                            <td>{{ $user->name }}</td>
                            @foreach ($packageNames as $packageName)
                                <td>{{ $packageCounts[$user->id][$packageName] ?? 0 }}</td>
                            @endforeach
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection