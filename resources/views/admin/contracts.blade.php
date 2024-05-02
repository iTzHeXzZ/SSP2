@extends('layouts.app')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
@section('content')
<style>
    .btn-group {
        display: flex; 
        gap: 5px; 
    }
    .table.custom-table tbody tr:nth-child(even) {
        background-color: #f8f9fa !important;
    }

    .table.custom-table tbody tr:nth-child(odd) {
        background-color: #fff !important;
    }
    .table.custom-table {
        --bs-table-bg: none;
    }
</style>
<div class="container">
    <div class="card mt-4">
        <div class="card-header">
            <h2>Auftragsbearbeitung</h2>
        </div>
        <div class="card-body">
            <div class="container">
                <table class="table custom-table">
                    <thead>
                        <tr>
                            <th>Benutzer</th>
                            <th>Kunde</th>
                            <th>Ort</th>
                            <th>Adresse</th>
                            <th>Status</th>
                            <th>Notiz</th>
                            <th>Aktion</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($contracts as $contract)
                            <tr>
                                <td>{{ $contract->user->name }}</td>
                                <td>{{ $contract->kundenname }}</td>
                                <td>{{ $contract->ort }}</td>
                                <td>{{ $contract->adresse }}</td>
                                <td>
                                    <select class="form-control status-dropdown" data-id="{{ $contract->id }}">
                                        <option value="Erstellt" {{ $contract->status == 'Erstellt' ? 'selected' : '' }}>Erstellt</option>
                                        <option value="Angenommen" {{ $contract->status == 'Angenommen' ? 'selected' : '' }}>Angenommen</option>
                                        <option value="Abgelehnt" {{ $contract->status == 'Abgelehnt' ? 'selected' : '' }}>Abgelehnt</option>
                                        <option value="Bearbeitung" {{ $contract->status == 'Bearbeitung' ? 'selected' : '' }}>Bearbeitung</option>
                                    </select>
                                </td>
                                <td><input type="text" class="form-control note-input" value="{{ $contract->notiz }}" data-id="{{ $contract->id }}"></td>
                                <td>
                                    <div class="btn-group">
                                        <button class="btn btn-primary save-changes" data-id="{{ $contract->id }}">Speichern</button>
                                        <button class="btn btn-danger delete-contract" data-id="{{ $contract->id }}"><i class="fas fa-trash-alt"></i></button>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                {{ $contracts->links() }}
            </div>
        </div>
    </div>
</div>
@endsection


@section('scripts')
<script>
document.querySelectorAll('.save-changes').forEach(button => {
    button.addEventListener('click', function() {
        const contractId = this.dataset.id;
        const status = document.querySelector(`.status-dropdown[data-id="${contractId}"]`).value;
        const note = document.querySelector(`.note-input[data-id="${contractId}"]`).value;

        fetch(`/update-contract/${contractId}`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ status, note })
        })
        .then(response => response.json())
        .then(data => {
            alert(data.message);
        })
        .catch(error => console.error('Error:', error));
    });
});

document.querySelectorAll('.delete-contract').forEach(button => {
    button.addEventListener('click', function() {
        const contractId = this.dataset.id;
        if (confirm('Sind Sie sicher, dass Sie diesen Auftrag löschen möchten?')) {
            fetch(`/delete-contract/${contractId}`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Content-Type': 'application/json'
                },
            })
            .then(response => response.json())
            .then(data => {
                alert(data.message);
                location.reload(); 
            })
            .catch(error => console.error('Error:', error));
        }
    });
});

</script>
@endsection
