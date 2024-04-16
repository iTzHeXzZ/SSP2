@extends('layouts.app')

@section('content')
@if (session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
@endif

@if (session('error'))
    <div class="alert alert-danger">
        {{ session('error') }}
    </div>
@endif

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Excel Datei importieren</div>

                <div class="card-body">
                    <form method="post" action="{{ route('import.projects') }}" enctype="multipart/form-data">
                        @csrf
                        <div class="form-group">
                            <label for="file">Bitte wählen:</label>
                            <input type="file" class="form-control-file" name="file" id="file" lang="de">
                        </div>
                        <button type="submit" class="btn btn-primary">Importieren</button>
                    </form>
                </div>
            </div>
        </div>
        <div class="col-md-8 mt-4">
            <div class="card">
                <div class="card-header">Struktur der Excel-Datei</div>
                <div class="card-body">
                    <p>Die erste Zeile muss die Spaltennamen enthalten!</p>
                    <p>Die Excel-Datei sollte folgende Struktur haben:</p>
                    <table class="table">
                        <thead>
                            <tr>
                                <th>ort</th>
                                <th>postleitzahl</th>
                                <th>strasse</th>
                                <th>hausnummer</th>
                                <th>wohneinheiten</th>
                                <th>bestand</th>
                                <th>notiz</th>
                                <th>status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>Musterort</td>
                                <td>12345</td>
                                <td>Musterstraße</td>
                                <td>12</td>
                                <td>5</td>
                                <td>2</td>
                                <td>(Kann leer bleiben)</td>
                                <td>(Kann leer bleiben)</td>
                            </tr>
                            <!-- Weitere Beispiele hier einfügen, falls erforderlich -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
