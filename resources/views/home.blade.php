@extends('layouts.app')

@section('content')
<style>
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
                    <form action="{{ route('home') }}" method="GET" class="form-inline" style="float: right;">
                        <div class="form-group">
                            <label for="start_date">Von: </label>
                            <input type="date" id="start_date" name="start_date" class="form-control mx-2" value="{{ $startDate }}" required>
                        </div>
                        <div class="form-group">
                            <label for="end_date">Bis: </label>
                            <input type="date" id="end_date" name="end_date" class="form-control mx-2" value="{{ $endDate }}" required>
                        </div>
                        <button type="submit" class="btn btn-primary">Filtern</button>
                    </form>
                </div>
                <div class="card-body">
                    <table class="table">
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
                    {{ $auftraege->links() }} <!-- Pagination links -->
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
