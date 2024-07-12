<!doctype html>
<html lang="de">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>VPP SSP</title>
    <style>
        /* Fügen Sie hier Ihren CSS-Stil für die Überschriften hinzu */
        th[data-sort] {
            cursor: pointer;
            color: #007bff; /* Ändern Sie die Farbe nach Ihren Wünschen */
            text-decoration: underline;
        }

        th[data-sort].desc::after {
            content: ' ↓'; /* Pfeilsymbol nach unten für absteigende Sortierung */
        }

        th[data-sort]:not(.desc)::after {
            content: ' ↑'; /* Pfeilsymbol nach oben für aufsteigende Sortierung */
        }
        #loadingOverlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            z-index: 1000;
        }
        /* Spinner-Stil */
        #loadingSpinner {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            color: white;
        }

    </style>

    <!-- Fonts -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    <link rel="dns-prefetch" href="//fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=Nunito" rel="stylesheet">
        <!-- CSS -->
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
        <!-- jQuery -->
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    
        <!-- Bootstrap JS -->
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    
        <!-- Bootstrap Multiselect CSS -->
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-multiselect/0.9.15/css/bootstrap-multiselect.css">
    
        <!-- Bootstrap Multiselect JS -->
        <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-multiselect/0.9.15/js/bootstrap-multiselect.min.js"></script>
        <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />

        <!-- Leaflet JavaScript -->
        <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>

    @vite(['resources/sass/app.scss', 'resources/js/app.js'])
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.30.1/moment-with-locales.min.js"></script>
    
    
    
</head>
<body>
    <div id="app">
        <nav class="navbar navbar-expand-md navbar-light bg-white shadow-sm">
            <div class="container">
                <a class="navbar-brand d-flex" href="{{ url('/home') }}">
                    <div><img src="/Images/ssp.png" style="max-height: 30px; border-right: 1px solid #333; padding-right : 6px " ></div>
                    <div style="padding-left: 6px">Safe-Start-Projects</div>
                </a>
                    @auth 
                    <a class="navbar-brand d-flex" href="{{ url('/projects') }}">
                        <div style="padding-left: 6px">Projekte</div>
                    </a>
                    <a class="navbar-brand d-flex" href="{{ route('analyse') }}">
                        <div style="padding-left: 6px">Analyse</div>
                    </a>
                    <a class="navbar-brand d-flex" href="{{ route('auswertung.show') }}">
                        <div style="padding-left: 6px">Auswertung</div>
                    </a>
                    @if(Auth::user() && Auth::user()->hasRole('Admin'))
                        <a class="navbar-brand d-flex" href="{{ route('pdf.contracts') }}">
                            <div style="padding-left: 6px">Vertragsverwaltung</div>
                        </a>
                        <a class="navbar-brand d-flex" href="{{ route('import.projects') }}">
                            <div style="padding-left: 6px">Projekt importieren</div>
                        </a>
                    @endif

                    @endauth
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="{{ __('Toggle navigation') }}">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <!-- Left Side Of Navbar -->
                    <ul class="navbar-nav me-auto">

                    </ul>

                    <!-- Right Side Of Navbar -->
                    <ul class="navbar-nav ms-auto">
                        <!-- Authentication Links -->
                        @guest
{{--                             @if (Route::has('login'))
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('login') }}">{{ __('Login') }}</a>
                                </li>
                            @endif

                           @if (Route::has('register'))
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('register') }}">{{ __('Register') }}</a>
                                </li>
                            @endif --}}
                        @else

                        @if (Auth::user()->hasrole('Admin'))
                                    <li><a class="nav-link" href="{{ route('users.index') }}">Benutzerverwaltung</a></li>
                                    <li><a class="nav-link" href="{{ route('roles.index') }}">Rollen</a></li>
                                    <li><a class="nav-link" href="{{ route('assign.form')}}">Straßenzuordnung</a></li>
                                    {{-- <li><a class="nav-link" href="{{ route('status.index')}}">Monitoring</a></li> --}}
                                    <li><a class="nav-link" href="{{ route('archived_projects.index') }}">Archiv</a></li>

                        @endif                          
                                    <li class="nav-item dropdown">
                                            <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                                                {{ Auth::user()->name }}
                                            </a>
                                <div class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                                    <a class="dropdown-item" href="{{ route('user.settings') }}"><strong>Profil</strong></a>
                                    <a class="dropdown-item" href="{{ route('logout') }}"
                                       onclick="event.preventDefault();
                                                     document.getElementById('logout-form').submit();">
                                        {{ __('Logout') }}
                                    </a>
                                    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                        @csrf
                                    </form>
                                </div>
                            </li>
                        @endguest
                    </ul>
                </div>
            </div>
        </nav>
        <div id="loadingOverlay">
            <div id="loadingSpinner" class="text-center">
                <i class="fas fa-spinner fa-spin fa-3x"></i>
                <p>Daten werden geladen...</p>
            </div>
        </div>

        <main class="py-4">
            @yield('content')
            @yield('scripts')
        </main>
    </div>
</body>
</html>
<script>
$(document).ready(function () {
    const sortTable = function (column, descending = false) {
        const $table = $('table');
        const $rows = $table.find('tbody tr').toArray();

        $rows.sort(function (a, b) {
            const valA = getValue($(a).find('td:eq(' + column + ')'));
            const valB = getValue($(b).find('td:eq(' + column + ')'));

            if (!isNaN(valA) && !isNaN(valB)) {
                return valA - valB;
            } else {
                return valA.toString().localeCompare(valB.toString());
            }
        });

        if (descending) {
            $rows.reverse();
        }

        $table.find('tbody').empty().append($rows);
    };

    const getValue = function ($cell) {
        const text = $cell.text().trim();

        const numericValue = parseFloat(text.replace(',', ''));
        if (!isNaN(numericValue)) {
            return numericValue;
        }

        const $link = $cell.find('a');
        if ($link.length > 0) {
            return $link.text();
        }

        return text;
    };

    sortTable(0);

    $('th[data-sort]').on('click', function () {
        const column = $(this).data('sort');
        const descending = $(this).hasClass('desc');

        sortTable(column, descending);

        $('th[data-sort]').removeClass('desc');
        if (descending) {
            $(this).removeClass('desc');
        } else {
            $(this).addClass('desc');
        }
    });

        function showLoadingOverlay() {
            $('#loadingOverlay').show();
        }

        function hideLoadingOverlay() {
            $('#loadingOverlay').hide();
        }
        $('a').on('click', function () {
            showLoadingOverlay();
        });

        $('form').on('submit', function () {
            showLoadingOverlay();
        });
        $('#navbarDropdown').on('click', function () {
            hideLoadingOverlay();
        });
        hideLoadingOverlay();
});
        
</script>

