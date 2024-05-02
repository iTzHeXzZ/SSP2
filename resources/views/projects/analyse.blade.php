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
                        <th>Unbesucht</th>
                        <th>Vertrag</th>
                        <th>Karte</th>
                        <th>Überleger</th>
                        <th>Kein Interesse</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($stats as $userId => $data)
                    <tr>
                        <td>{{ optional(App\Models\User::find($userId))->name ?? 'Benutzer nicht gefunden' }}</td>
                        <td>{{ $unbesuchte_counts[$userId] ?? 0 }}</td>
                        <td><a style="text-decoration : none" href="#" class="status-link" data-user-id="{{ $userId }}" data-status="Vertrag">{{ $data['Vertrag'] ?? 0 }}</a></td>
                        <td><a style="text-decoration : none" href="#" class="status-link" data-user-id="{{ $userId }}" data-status="Karte">{{ $data['Karte'] ?? 0 }}</a></td>
                        <td><a style="text-decoration : none" href="#" class="status-link" data-user-id="{{ $userId }}" data-status="Überleger">{{ $data['Überleger'] ?? 0 }}</a></td>
                        <td><a style="text-decoration : none" href="#" class="status-link" data-user-id="{{ $userId }}" data-status="Kein Interesse">{{ $data['Kein Interesse'] ?? 0 }}</a></td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    <div>    <div id="details-container" class="mt-4"></div>
    <div id="pagination-container" class="mt-4"></div></div>
</div>

@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const links = document.querySelectorAll('.status-link');

    links.forEach(link => {
        link.addEventListener('click', function (e) {
            e.preventDefault();
            const userId = this.getAttribute('data-user-id');
            const status = this.getAttribute('data-status');
            const startDate = document.getElementById('start_date').value;
            const endDate = document.getElementById('end_date').value;
            const container = document.getElementById('details-container');
            const paginationContainer = document.getElementById('pagination-container');
            console.log('startDate:', startDate);
    console.log('endDate:', endDate);

            const url = `/get-project-details/${userId}/${status}?start_date=${startDate}&end_date=${endDate}`;

            fetch(url)
                .then(response => response.json())
                .then(data => {
                        let content = '<div class="card"><div class="card-body"><table class="table"><thead><tr><th>Status</th><th>Ort</th><th>PLZ</th><th>Straße</th><th Hausnummer</th></tr></thead><tbody>';
                        data.data.forEach(project => {
                            content += `<tr>
                                <td>${project.status}</td>
                                <td>${project.ort}</td>
                                <td>${project.postleitzahl}</td>
                                <td>${project.strasse}</td>
                                <td>${project.hausnummer}</td>
                            </tr>`;
                        });
                        content += '</tbody></table>';

                        let paginationHtml = '<div class="pagination-container">';
                        if (data.pagination.links.prev) {
                            paginationHtml += `<a href="#" class="btn btn-primary" onclick="event.preventDefault(); loadPage('${data.pagination.links.prev}');">Previous</a> `;
                        }
                        if (data.pagination.links.next) {
                            paginationHtml += `<a href="#" class="btn btn-primary" onclick="event.preventDefault(); loadPage('${data.pagination.links.next}');">Next</a>`;
                        }
                        paginationHtml += '</div>';

                        content += paginationHtml + '</div></div>'; 
                        container.innerHTML = content;
                    })
                .catch(error => console.error('Error:', error));
        });
    });
});
window.loadPage = function(url) {
    const startDate = document.getElementById('start_date').value;
    const endDate = document.getElementById('end_date').value;
    const separator = url.includes('?') ? '&' : '?';
    const urlWithDates = `${url}${separator}start_date=${startDate}&end_date=${endDate}`;
    const container = document.getElementById('details-container');
    const paginationContainer = document.getElementById('pagination-container');


    fetch(urlWithDates)
        .then(response => response.json())
        .then(data => {
                let content = '<div class="card"><div class="card-body"><table class="table"><thead><tr><th>Status</th><th>Ort</th><th>PLZ</th><th>Straße</th><th Hausnummer</th></tr></thead><tbody>';
                data.data.forEach(project => {
                    content += `<tr>
                        <td>${project.status}</td>
                        <td>${project.ort}</td>
                        <td>${project.postleitzahl}</td>
                        <td>${project.strasse}</td>
                        <td>${project.hausnummer}</td>
                    </tr>`;
                });
                content += '</tbody></table>';

                let paginationHtml = '<div class="pagination-container">';
                if (data.pagination.links.prev) {
                    paginationHtml += `<a href="#" class="btn btn-primary" onclick="event.preventDefault(); loadPage('${data.pagination.links.prev}');">Previous</a> `;
                }
                if (data.pagination.links.next) {
                    paginationHtml += `<a href="#" class="btn btn-primary" onclick="event.preventDefault(); loadPage('${data.pagination.links.next}');">Next</a>`;
                }
                paginationHtml += '</div>';

                content += paginationHtml + '</div></div>'; 
                container.innerHTML = content;
            })
        .catch(error => console.error('Error:', error));
};

</script>
@endsection
