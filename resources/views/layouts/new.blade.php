@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Projekt und Straßen einem Benutzer zuweisen</div>

                <div class="card-body">
                    <form method="POST" action="{{ route('assign.project') }}">
                        @csrf

                        <!-- Andere Formularelemente hier einfügen -->

                        <div class="form-group">
                            <label for="street">Straßen auswählen:</label>
                            <select name="streets[]" id="street" class="form-control" multiple>
                                <!-- Hier werden die Straßenoptionen eingefügt -->
                                <option value="Street1">Street1</option>
                                <option value="Street2">Street2</option>
                                <option value="Street3">Street3</option>
                                <!-- Fügen Sie hier weitere Straßenoptionen ein -->
                            </select>
                        </div>

                        <!-- Weitere Formularelemente hier einfügen -->

                        <button type="submit" class="btn btn-primary">Projekt und Straße zuweisen</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        $('#street').multiselect({
            enableFiltering: true, // Aktiviert die Suchfunktion
            maxHeight: 300, // Maximale Höhe des Dropdown-Menüs
        });
    });
</script>
@endsection
