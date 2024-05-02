@extends('layouts.app')

@section('content')
<div class="container">
    <div class="card mt-4">
        <div class="card-header">
            <h2>Auftragsauswertung</h2>
        </div>
        <div class="card-body">
            <form id="date-form" class="mb-4">
                <div class="form-row">
                    <div class="col-md-4">
                        <label for="start_date">Startdatum:</label>
                        <input type="date" id="start_date" name="start_date" value="{{ request('start_date', now()->startOfMonth()->toDateString()) }}" class="form-control">
                    </div>
                    <div class="col-md-4">
                        <label for="end_date">Enddatum:</label>
                        <input type="date" id="end_date" name="end_date" value="{{ request('end_date', now()->endOfMonth()->toDateString()) }}" class="form-control">
                    </div>
                    <div class="col-md-4">
                        <button type="submit" class="btn btn-primary mt-4">Filtern</button>
                    </div>
                </div>
            </form>

            <table class="table mt-4" id="data-table">
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
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('date-form');
    const table = document.getElementById('data-table');

    form.addEventListener('submit', function (e) {
        e.preventDefault();

        const startDate = document.getElementById('start_date').value;
        const endDate = document.getElementById('end_date').value;

        fetch(`/get-user-and-order-data?start_date=${startDate}&end_date=${endDate}`)
            .then(response => response.json())
            .then(data => {
                const tbody = table.querySelector('tbody');
                tbody.innerHTML = '';

                data.users.forEach(user => {
                    const row = document.createElement('tr');
                    row.innerHTML = `<td>${user.name}</td>`;
                    data.packageNames.forEach(packageName => {
                        const cell = document.createElement('td');
                        cell.textContent = data.packageCounts[user.id][packageName] || 0;
                        row.appendChild(cell);
                    });
                    tbody.appendChild(row);
                });
            })
            .catch(error => console.error('Error:', error));
    });
});

</script>
@endsection
