@extends('layouts.app')

@section('content')
    <h1>Straßen von {{ $user->name }} in {{ $ort }}, {{ $postleitzahl }}</h1>

    <ul>
        @foreach ($streets as $street)
            <li>{{ $street }}</li>
        @endforeach
    </ul>
@endsection
