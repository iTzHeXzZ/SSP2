@extends('layouts.app') <!-- Wenn du ein Layout verwendest -->

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Benutzereinstellungen</div>

                <div class="card-body">
                    <form method="POST" action="{{ route('user.settings.update') }}">
                        @csrf
                        <!-- Hier das Formular für die Benutzereinstellungen einfügen -->
                        <div class="form-group">
                            <label for="name">Name</label>
                            <input type="text" name="name" id="name" value="{{ Auth::user()->name }}" class="form-control">
                        </div>
                        <div class="form-group">
                            <label for="email">E-Mail</label>
                            <input type="email" name="email" id="email" value="{{ Auth::user()->email }}" class="form-control">
                        </div>
                        <div class="form-group">
                            <label for="password">Passwort</label>
                            <input type="password" name="password" id="password" class="form-control">
                        </div>
                        <div class="form-group">
                            <label for="password_confirmation">Passwort bestätigen</label>
                            <input type="password" name="password_confirmation" id="password_confirmation" class="form-control">
                        </div>
                        <button type="submit" class="btn btn-primary">Speichern</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
