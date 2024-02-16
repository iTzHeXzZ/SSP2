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
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
