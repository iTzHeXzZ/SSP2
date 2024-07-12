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
                        <th>Fremd VP</th>
                        <th>Karte</th>
                        <th>Überleger</th>
                        <th>Kein Interesse</th>
                        <th>Kein Potenzial</th>
                        @if (auth()->user()->hasRole('Admin')) <th>% Verträge/Bewegung</th> @endif
                    </tr>
                </thead>
                <tbody>
                    @foreach ($stats as $userId => $data)
                    <tr>
                        @php
                            $dataArray = $data->toArray();
                            $total = array_sum($dataArray);
                            $vertragCount = $dataArray['Vertrag'] ?? 0;
                            $vertragPercentage = ($vertragCount / $total) * 100;
                        @endphp
                        @php
                            $user = App\Models\User::find($userId);
                        @endphp
                        <td>
                            @if ($user && auth()->user()->hasRole('Admin'))
                                <a href="#" class="user-link" data-user-id="{{ $userId }}">{{ $user->name }}</a>
                            @else
                                {{ $user ? $user->name : 'Benutzer nicht gefunden' }}
                            @endif
                        </td>
                        <td>{{ $unbesuchte_counts[$userId] ?? 0 }}</td>
                        <td><a style="text-decoration : none" href="#" class="status-link" data-user-id="{{ $userId }}" data-status="Vertrag">{{ $data['Vertrag'] ?? 0 }}</a></td>
                        <td><a style="text-decoration : none" href="#" class="status-link" data-user-id="{{ $userId }}" data-status="Fremd VP">{{ $data['Fremd VP'] ?? 0 }}</a></td>
                        <td><a style="text-decoration : none" href="#" class="status-link" data-user-id="{{ $userId }}" data-status="Karte">{{ $data['Karte'] ?? 0 }}</a></td>
                        <td><a style="text-decoration : none" href="#" class="status-link" data-user-id="{{ $userId }}" data-status="Überleger">{{ $data['Überleger'] ?? 0 }}</a></td>
                        <td><a style="text-decoration : none" href="#" class="status-link" data-user-id="{{ $userId }}" data-status="Kein Interesse">{{ $data['Kein Interesse'] ?? 0 }}</a></td>
                        <td><a style="text-decoration : none" href="#" class="status-link" data-user-id="{{ $userId }}" data-status="Kein Potenzial">{{ $data['Kein Potenzial'] ?? 0 }}</a></td>
                        @if (auth()->user()->hasRole('Admin')) <td>{{ number_format($vertragPercentage, 2) }}%</td> @endif
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    <div>    <div id="details-container" class="mt-4"></div>
    <div id="pagination-container" class="mt-4"></div></div>
</div>
<div class="modal fade" id="projectAnalysisModal" tabindex="-1" aria-labelledby="projectAnalysisModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="projectAnalysisModalLabel">Projektanalyse für <span id="modalUsername"></span></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="projectAnalysisContent"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Schließen</button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
            function showLoadingOverlay() {
            $('#loadingOverlay').show();
        }
    
        function hideLoadingOverlay() {
            $('#loadingOverlay').hide();
        }
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
                        hideLoadingOverlay();
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
                hideLoadingOverlay();
            })
        .catch(error => console.error('Error:', error));
};
document.addEventListener('DOMContentLoaded', function () {
    const userLinks = document.querySelectorAll('.user-link');

    userLinks.forEach(link => {
        link.addEventListener('click', function (e) {
            e.preventDefault();
            const userId = this.getAttribute('data-user-id');
            const modalUsername = this.textContent.trim();
            const container = document.getElementById('projectAnalysisContent');
            const modalUsernameElement = document.getElementById('modalUsername');

            modalUsernameElement.textContent = modalUsername;

            const url = `/get-project-analysis/${userId}`;

            fetch(url)
            .then(response => response.json())
            .then(data => {
                let content = '<table class="table"><thead><tr><th>Status</th><th>Anzahl</th></tr></thead><tbody>';
                Object.entries(data).forEach(([status, count]) => {
                    content += `<tr><td>${status}</td><td>${count}</td></tr>`;
                });
                console.log(data);
                content += '</tbody></table>';

                container.innerHTML = content;

                $('#projectAnalysisModal').modal('show');
                hideLoadingOverlay();
            })
            .catch(error => console.error('Error:', error));
        });
    });
});
</script>
@endsection
