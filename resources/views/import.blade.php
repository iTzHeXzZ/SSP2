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
                                <input type="file" class="form-control-file" name="file" id="file">
                            </div>
                            <button type="submit" class="btn btn-primary">Importieren</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
