@extends('layouts.app')

@section('content')
<style>
    .table.custom-table tbody tr:nth-child(even) {
        background-color: #f8f9fa !important;
    }

    .table.custom-table tbody tr:nth-child(odd) {
        background-color: #fff !important;
    }
    .table.custom-table {
        --bs-table-bg: none;
    }
    .link-item {
        margin-bottom: 10px; 
    }

    .link-item a {
        text-decoration: none;
        color: #333;
        display: flex;
        align-items: center; 
    }

    .link-item a:hover {
        text-decoration: underline;
    }

    .link-item a:visited {
        color: #555;
    }

    .link-icon {
        margin-right: 5px; 
    }

    .dashboard-card {
        background-color: #e9ecef;
    }
    .pagination-wrapper {
        display: flex;
        justify-content: space-between;
    }

    .pagination-info {
        color: rgba(0, 0, 0, 0.5);
        font-size: 0.9em;
        align-items: center;
        margin-top: 8px;
        margin-right: 5px
    }

    .pagination-links {
        display: flex;
        align-items: center;
    }

</style>

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card dashboard-card">
                <div class="card-header">{{ __('Dashboard') }}</div>
                
                <div class="card-body" style="background-color: #f8f9fa;">
                    <div class="card">
                        <div class="card-header bg-light">UGG Links:</div>
                        <div class="card-body" style="background-color: #fff;">
                            <div class="link-item">
                                <a href="{{ url('https://ugg.vpp.get-ag.com/') }}" target="_blank">
                                    <span class="link-icon">&#128279;</span>
                                    UGG Login
                                </a>
                            </div>
                            <div class="link-item">
                                <a href="{{ url('https://unseregrueneglasfaser.de/vereinbarung-zum-glasfaseranschluss-mehrfamilienhaus/') }}" target="_blank">
                                    <span class="link-icon">&#128279;</span>
                                    UGG GEE bis 3 WE
                                </a>
                            </div>
                            <div class="link-item">
                                <a href="{{ url('https://unseregrueneglasfaser.de/grundstueckseigentuemererklaerung') }}" target="_blank">
                                    <span class="link-icon">&#128279;</span>
                                    UGG GEE ab 4 WE
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-body" style="background-color: #f8f9fa;">
                    <div class="card">
                        <div class="card-header bg-light">UGG Datein:</div>
                        <div class="card-body" style="background-color: #fff;">
                            <div class="link-item">
                                <a href="{{ route('download', 'o2auftrag.pdf') }}" target="_blank">
                                    <span class="link-icon">&#128229;</span>
                                    O2 Auftrag herunterladen
                                </a>
                            </div>
                            <div class="link-item">
                                <a href="{{ route('download', 'geesdu.pdf') }}" target="_blank">
                                    <span class="link-icon">&#128229;</span>
                                    GEE bis 3 WE
                                </a>
                            </div>
                            <div class="link-item">
                                <a href="{{ route('download', 'geemfh.pdf') }}" target="_blank">
                                    <span class="link-icon">&#128229;</span>
                                    GEE ab 4 WE
                                </a>
                            </div>
                            <div class="link-item">
                                <a href="{{ route('download', 'flyer.pdf') }}" target="_blank">
                                    <span class="link-icon">&#128229;</span>
                                    Preisflyer
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-body" style="background-color: #f8f9fa;">
                    <div class="card">
                        <div class="card-header bg-light">UGG Papierauftrag erfassen:</div>
                        <div class="card-body" style="background-color: #fff;">
                            <div class="link-item">
                                <a href="{{ url('/pdf/uggform') }}" target="_blank">
                                    <span class="link-icon">&#128230;</span> 
                                    Auftrag erfassen
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    {{ __('Meine Aufträge') }}
                    <form id="date-form" action="{{ route('home') }}" method="GET" class="form-inline" style="float: right;">
                        @csrf
                        <div class="form-group">
                            <label for="start_date">Von: </label>
                            <input type="date" id="start_date" name="start_date" class="form-control mx-2" value="{{ request('start_date', now()->startOfMonth()->toDateString()) }}" required>
                        </div>
                        <div class="form-group">
                            <label for="end_date">Bis: </label>
                            <input type="date" id="end_date" name="end_date" class="form-control mx-2" value="{{ request('end_date', now()->endOfMonth()->toDateString()) }}" required>
                        </div>
                        <button type="submit" class="btn btn-primary">Filtern</button>
                    </form>
                </div>
                <div class="card-body">
                    <table class="table custom-table" id="data-table">
                        <thead>
                            <tr>
                                <th>Erstellungsdatum</th>
                                <th>Ort</th>
                                <th>Adresse</th>
                                <th>Kundenname</th>
                                <th>Status</th>
                                <th>Notiz</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($auftraege as $auftrag)
                                <tr>
                                    <td>{{ $auftrag->created_at->format('d.m.Y') }}</td>
                                    <td>{{ $auftrag->ort }}</td>
                                    <td>{{ $auftrag->adresse }}</td>
                                    <td>{{ $auftrag->kundenname }}</td>
                                    <td>{{ $auftrag->status }}</td>
                                    <td>{{ $auftrag->notiz }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6">Keine Aufträge gefunden.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                    <div id="pagination-container">
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
            const form = document.getElementById('date-form');

            if (form) {
                form.addEventListener('submit', function (e) {
                    e.preventDefault();
                    const startDate = document.getElementById('start_date').value;
                    const endDate = document.getElementById('end_date').value;
                    const url = `/home?start_date=${startDate}&end_date=${endDate}`;
                    fetchData(url, startDate, endDate);
                });
            }
            const initialStartDate = document.getElementById('start_date').value;
            const initialEndDate = document.getElementById('end_date').value;
            const initialUrl = `/home?start_date=${initialStartDate}&end_date=${initialEndDate}`;
            fetchData(initialUrl, initialStartDate, initialEndDate);
        });

        async function fetchData(url, startDate, endDate) {
            try {
                const response = await fetch(url, {
                    headers: {
                        'Accept': 'application/json'
                    }
                });
                if (!response.ok) {
                    throw new Error('Failed to fetch data');
                }
                const data = await response.json();
                updateTable(data);
                updatePagination(data.pagination, startDate, endDate);
            } catch (error) {
                console.error('Error:', error);
            }
        }


        function updateTable(data) {
            const tbody = document.querySelector('tbody');
            tbody.innerHTML = '';
            console.log(data.data);
            if (data.data.data.length > 0) {
                data.data.data.forEach(auftrag => {
                    const row = document.createElement('tr');
                    row.innerHTML = `
                        <td>${formatDate(auftrag.created_at)}</td>
                        <td>${auftrag.ort}</td>
                        <td>${auftrag.adresse}</td>
                        <td>${auftrag.kundenname}</td>
                        <td>${auftrag.status}</td>
                        <td>${auftrag.notiz}</td>
                    `;
                    tbody.appendChild(row);
                });
            } else {
                const row = document.createElement('tr');
                row.innerHTML = `
                    <td colspan="6">Keine Aufträge gefunden.</td>
                `;
                tbody.appendChild(row);
            }
            
            updatePagination(data.pagination);
        }

        function updatePagination(pagination, startDate, endDate) {
            const paginationContainer = document.getElementById('pagination-container');
            paginationContainer.innerHTML = '';

            if (pagination && pagination.links) {
                const paginationWrapper = document.createElement('div');
                paginationWrapper.classList.add('pagination-wrapper');
                paginationContainer.appendChild(paginationWrapper);

                const paginationLinksContainer = document.createElement('div');
                paginationLinksContainer.classList.add('pagination-links');
                paginationWrapper.appendChild(paginationLinksContainer);

                if (pagination.links.prev) {
                    const prevPageLink = createPaginationLink(pagination.links.prev, 'Zurück', startDate, endDate);
                    paginationLinksContainer.appendChild(prevPageLink);
                }

                Object.keys(pagination.links).forEach(label => {
                    if (label !== 'prev' && label !== 'next') {
                        const pageLink = createPaginationLink(pagination.links[label], label, startDate, endDate);
                        paginationLinksContainer.appendChild(pageLink);
                    }
                });

                if (pagination.links.next) {
                    const nextPageLink = createPaginationLink(pagination.links.next, 'Weiter', startDate, endDate);
                    paginationLinksContainer.appendChild(nextPageLink);
                }
                
                const rangeInfo = document.createElement('span');
                const fromIndex = (pagination.current_page - 1) * pagination.per_page + 1;
                const toIndex = Math.min(pagination.current_page * pagination.per_page, pagination.total);
                rangeInfo.textContent = `Zeige ${fromIndex}-${toIndex} von ${pagination.total}`;
                rangeInfo.classList.add('pagination-info');
                paginationWrapper.appendChild(rangeInfo);
            }
        }



        function createPaginationLink(url, text, startDate, endDate, isButton = false) {
            const container = isButton ? document.createElement('div') : document.createElement('span');
            if (isButton) {
                container.classList.add('pagination-button-container');
            }
            const link = document.createElement('a');
            if (url !== null) {
                if (url.includes('?')) {
                    link.href = `${url}&start_date=${startDate}&end_date=${endDate}`;
                } else {
                    link.href = `${url}?start_date=${startDate}&end_date=${endDate}`;
                }
            } else {
                link.href = '#';
            }
            link.textContent = text;
            link.classList.add('btn', 'btn-primary','mr-2');
            link.style.alignSelf = 'center';
            link.addEventListener('click', function (e) {
                e.preventDefault();
                if (url !== '#') {
                    fetchData(link.href, startDate, endDate);
                }
            });
            container.appendChild(link);
            return container;
        }

        function formatDate(dateString) {
            const date = new Date(dateString);
            const options = { year: 'numeric', month: '2-digit', day: '2-digit', hour: '2-digit', minute: '2-digit' };
            return date.toLocaleDateString('de-DE', options);
        }
    </script>
@endsection