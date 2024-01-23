@extends('layouts.app') {{-- Beispiel für ein Master-Layout --}}

@section('content')
    <div class="alert alert-danger">
        Ein Fehler ist aufgetreten. Bitte versuche es später erneut.

        @if (isset($exception))
            <pre>{{ $exception }}</pre>
        @endif
    </div>
@endsection
