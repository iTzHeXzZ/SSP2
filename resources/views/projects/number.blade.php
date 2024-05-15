@extends('layouts.app')

@section('content')


<style>
    .feedback-message {
position: fixed;
top: 50%;
left: 50%;
transform: translate(-50%, -50%);
padding: 10px;
color: #fff;
font-size: 16px;
border-radius: 5px;
z-index: 999;
}

.table-header-sticky {
    position: sticky;
        top: 0;
        background-color: #f8f9fa; 
        border-bottom: 2px solid #dee2e6; 
        font-weight: bold; 
        padding: 10px; 
        text-align: left; 
        z-index: 1000;
    }

    .table tbody tr:nth-child(even) {
        background-color: #f2f2f2; /* Lighter color for even rows */
    }
    .table tbody tr:nth-child(odd) {
        background-color: #ffffff; /* White color for odd rows */
    }
    
.feedback-message.success {
background-color: #28a745; /* Grüner Hintergrund für Erfolgsmeldung */
}

.feedback-message.error {
background-color: #dc3545; /* Roter Hintergrund für Fehlermeldung */
}


/* Anpassung des td-Elements für relative Positionierung */
.table td.status-vertrag, 
.table td.status-kein-interesse {
    position: relative; /* Wichtig für die absolute Positionierung des Kindes */
    padding-right: 10px; /* Stellen Sie sicher, dass der Text nicht über den Streifen geht */
}

/* Vollständige Höhe für ::after */
.table td.status-vertrag::after, 
.table td.status-kein-interesse::after {
    content: "";
    position: absolute; /* Absolut innerhalb der Zelle positioniert */
    right: 0;
    top: 5px;
    width: 5px; /* Breite des Streifens */
    height: 95%; /* Volle Höhe der Zelle */
    background-color: inherit; /* Verwendet die Hintergrundfarbe des Elternelements */
}

/* Setzen der spezifischen Hintergrundfarben */
.table td.status-vertrag::after {
    background-color: #007d1d; /* Grün */
}

.table td.status-kein-interesse::after {
    background-color: #a70011; /* Rot */
}



    /* Stil für das Formular innerhalb der Zelle */
    form {
        display: flex;
        flex-direction: column;
        align-items: flex-start;
    }

    /* Stil für das Select-Feld */
    select {
        padding: 8px;
        margin-bottom: 10px;
        border: 1px solid #ccc;
        border-radius: 4px;
        width: 100%;
    }

    /* Stil für das Textarea-Feld */
    textarea {
        padding: 8px;
        margin-bottom: 10px;
        border: 1px solid #ccc;
        border-radius: 4px;
        width: 100%;
        resize: vertical;
        min-height: 100px;
    }

    /* Stil für den Speichern-Button */
    .btn-primary {
        background-color: #007bff;
        color: white;
        padding: 10px 15px;
        border: none;
        border-radius: 4px;
        cursor: pointer;
    }

    .btn-primary:hover {
        background-color: #0056b3;
    }

    @media (max-width: 768px) {
        form {
            flex-direction: column; /* Formular-Inhalte in Spalten anordnen */
        }

        select {
            width: 100%; /* Feld auf volle Breite */
        }

        textarea {
            width: 100%; /* Feld auf volle Breite */
        }

        .form-group {
            overflow-x: auto; /* Horizontales Scrollen ermöglichen */
            width: 100%; /* Breite auf 100% setzen */
        }
    }
</style>
<div class="modal fade" id="statusChangesModal" tabindex="-1" aria-labelledby="statusChangesModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="statusChangesModalLabel">Statusänderungen</h5>
            </div>
            <div class="modal-body" id="statusChangesModalBody">
            </div>
        </div>
    </div>
</div>
    <h2>{{$postleitzahl}},<a href="{{ route('projects.street', ['ort' => $ort, 'postleitzahl' => $postleitzahl]) }}" style="text-decoration : none">{{ $ort }}</a>,{{ $strasse }} Hausnummer:</h2>

    <table class="table">
        <thead class="card-header table-header-sticky">
            <tr>
                <th data-sort="0">Nr.</th>
                <th data-sort="1">Status</th>
                <th data-sort="2">WE</th>
                <th data-sort="3">BK</th>
                <th data-sort="4">Datum</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($projects as $project)
            @php
                $rowClass = $loop->index % 2 == 0 ? 'even-row' : 'odd-row';
                $statusClass = '';
                if ($project->status === 'Vertrag') {
                    $statusClass = 'status-vertrag';
                } elseif ($project->status === 'Kein Interesse') {
                    $statusClass = 'status-kein-interesse';
                }
            @endphp
            <tr class="{{ $rowClass }}">
                <td class="{{ $statusClass }}">{{ $project->hausnummer }}</td>
                <td>
                    <form id="projectForm_{{ $project->id }}" class="ajax-form" data-ort="{{ $ort }}" data-hausnummer="{{ $project->hausnummer }}" data-wohnung="1" data-auto-submit="false">
                        @csrf
                        <input type="hidden" name="strasse" value="{{ $strasse }}">
                        <input type="hidden" name="plz" value="{{ $postleitzahl }}">
                        <select name="status" onchange="handleVertragSelect(this, '{{ $ort }}', '{{ $project->hausnummer }}', '1')">
                            @foreach ($statusOptions as $option)
                                <option value="{{ $option }}" {{ $project->status === $option ? 'selected' : '' }}>
                                    {{ $option }}
                                </option>
                            @endforeach
                        </select>
                        @if (!auth()->user()->hasRole('Viewer'))
                            <textarea name="notiz">{{ $project->notiz }}</textarea>
                            <div style="display: flex; align-items: center;">
                                <button type="submit" class="btn btn-primary" onclick="submitFormViaAjax(this.form)">Speichern</button>
                                <a href="#" class="btn" onclick="selectAndSave('Karte', '{{ $ort }}', '{{ $project->hausnummer }}', '1')" style="display: inline-block; margin-left: 5px; vertical-align: middle;">
                                    <img src="/Images/visitenkarte.png" alt="Visitenkarte Symbol" style="width: 35px; height: 30px;">
                                </a>                                    
                            </div>
                        @endif
                    </form>
                    @for ($i = 2; $i <= $project->wohneinheiten; $i++)
                    @php
                        $subProject = $project->subProjects()->where('wohnung_nr', $i)->first();
                        $status = $subProject ? $subProject->status : 'Unbesucht';
                        $notiz = $subProject ? $subProject->notiz : '';
                    @endphp
                    <form class="mt-2" id="projectForm_{{ $project->id }}_{{ $i }}" class="ajax-form" data-ort="{{ $ort }}" data-hausnummer="{{ $project->hausnummer }}" data-wohnung="{{ $i }}" data-auto-submit="false">
                        @csrf
                        <input type="hidden" name="strasse" value="{{ $strasse }}">
                        <input type="hidden" name="plz" value="{{ $postleitzahl }}">
                        <select name="status_{{ $i }}" onchange="handleVertragSelect(this, '{{ $ort }}', '{{ $project->hausnummer }}', '{{ $i }}')">
                            @foreach ($statusOptions as $option)
                                <option value="{{ $option }}" {{ $status === $option ? 'selected' : '' }}>
                                    {{ $option }}
                                </option>
                            @endforeach
                        </select>
                        @if (!auth()->user()->hasRole('Viewer'))
                            <textarea name="notiz_{{ $i }}">{{ $notiz }}</textarea>
                            <div style="display: flex; align-items: center;">
                                <button type="submit" class="btn btn-primary" onclick="submitFormViaAjax(this.form)">Speichern</button>
                                <a href="#" class="btn" onclick="selectAndSave('Karte', '{{ $ort }}', '{{ $project->hausnummer }}', '{{ $i }}')" style="display: inline-block; margin-left: 5px; vertical-align: middle;">
                                    <img src="/Images/visitenkarte.png" alt="Visitenkarte Symbol" style="width: 35px; height: 30px;">
                                </a>                                    
                            </div>
                        @endif
                    </form>
                    @endfor
                </td>
                <td>{{ $project->wohneinheiten }}</td>
                <td>{{ $project->bestand }}</td>
                <td onclick="showStatusChanges('{{ $project->id }}')" 
                    data-project-id="{{ $project->id }}" 
                    data-status-logs="{{ json_encode($project->statusLogs) }}">
                    {{ \Carbon\Carbon::parse($project->updated_at)->isoFormat('DD.MM.YYYY HH:mm') }}
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    
@endsection

@section('scripts')
<script>
    function submitFormViaAjax(form) {
        if (form.dataset.autoSubmit === 'false') {
            event.preventDefault();
        }
        const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
    
        axios.defaults.headers.common['X-CSRF-TOKEN'] = csrfToken;
        const formData = new FormData(form);
        const projectId = form.id.split('_')[1];
    
        axios.post(`/projects/update/${projectId}`, formData)
            .then(response => {
                console.log(response.data);
                showFeedbackMessage('Speichern erfolgreich!', 'success');
                form.dataset.autoSubmit = 'false';
            })
            .catch(error => {
                console.error(error);
                showFeedbackMessage('Fehler beim Speichern!', 'error');
            });
    }
    
    function showFeedbackMessage(message, type) {
        const feedbackContainer = document.createElement('div');
        feedbackContainer.className = `feedback-message ${type}`;
        feedbackContainer.textContent = message;
    
        document.body.appendChild(feedbackContainer);
    
        setTimeout(() => {
            feedbackContainer.remove();
        }, 2000);
    }
    
    function selectAndSave(status, ort, hausnummer, wohnung) {
        // Sucht das zugehörige Formular zum Dropdown
        const form = document.querySelector(`form[data-ort="${ort}"][data-hausnummer="${hausnummer}"][data-wohnung="${wohnung}"]`);
    
        if (form) {
            // Setzt den Dropdown-Wert auf "Karte"
            form.querySelector(`select[name="status_${wohnung}"]`).value = status;
    
            // Extrahiere die Projekt-ID aus dem Formular-ID-Attribut
            const projectId = form.id.split('_')[1];
    
            // Automatisches Absenden des Formulars mit AJAX
            axios.post(`/projects/update/${projectId}`, new FormData(form))
                .then(response => {
                    // Erfolgreiche Antwort verarbeiten, falls nötig
                    console.log(response.data);
    
                    showFeedbackMessage('Speichern erfolgreich!', 'success');
                })
                .catch(error => {
                    // Fehler verarbeiten, falls nötig
                    console.error(error);
    
                    showFeedbackMessage('Fehler beim Speichern!', 'error');
                })
                .finally(() => {
                    restoreScrollPosition();
                });
        } else {
            console.error('Formular nicht gefunden');
        }
    }
    
    
    function restoreScrollPosition() {
        console.log('restoreScrollPosition called');
        
        // Speichere die aktuelle Scroll-Position, bevor du die Seite verlässt
        const scrollPosition = window.scrollY;
        console.log('Scroll Position vor dem Speichern:', scrollPosition);
        
        localStorage.setItem('scrollPosition', scrollPosition);
    
        // Rufe die gespeicherte Scroll-Position ab und scrolle zur Position zurück
        const storedScrollPosition = localStorage.getItem('scrollPosition');
        console.log('Gespeicherte Scroll Position:', storedScrollPosition);
    
        if (storedScrollPosition !== null) {
            window.scrollTo(0, storedScrollPosition);
            localStorage.removeItem('scrollPosition');
        }
    }
    
    // Rufe die restoreScrollPosition-Funktion direkt nach dem Laden der Seite auf
    document.addEventListener('DOMContentLoaded', () => {
        restoreScrollPosition();
    });
    
    
    
    function handleVertragSelect(selectElement, ort, hausnummer, wohnung) {
    var status = selectElement.value;
    const form = selectElement.form;
    const strasse = form.querySelector('input[name="strasse"]').value;
    const plz = form.querySelector('input[name="plz"]').value;
    const queryParams = `?ort=${ort}&hausnummer=${hausnummer}&wohnung=${wohnung}&strasse=${strasse}&plz=${plz}`;
    
    if (status === 'Vertrag' && ort.includes('Langenfeld')) {
        const url = "/pdf/showForm" + queryParams;

        const newWindow = window.open(url, "_blank");

        let checkWindow = setInterval(() => {
            if (newWindow.closed) {
                clearInterval(checkWindow);
                submitStatusAsVertrag(ort, hausnummer, wohnung);
            }
        }, 500);
    } else {
        if (wohnung === '1'){
            submitStatusForMainProject(ort, hausnummer, status);
        } else {
            submitStatus(ort, hausnummer, wohnung, status);
        }
    }
}

    function submitStatusForMainProject(ort, hausnummer, status) {
    const form = document.querySelector(`form[data-ort="${ort}"][data-hausnummer="${hausnummer}"][data-wohnung="1"]`);
    if (form) {
        form.dataset.autoSubmit = 'false';
        const selectElement = form.querySelector('select[name="status"]');
        if (selectElement) {
            selectElement.value = status;
            submitFormViaAjax(form);
        } else {
            console.error('Dropdown-Menü nicht gefunden');
        }
    } else {
        console.error('Formular nicht gefunden');
    }
}

    function submitStatus(ort, hausnummer, wohnung, status) {
    const form = document.querySelector(`form[data-ort="${ort}"][data-hausnummer="${hausnummer}"][data-wohnung="${wohnung}"]`);
    if (form) {
        form.dataset.autoSubmit = 'false';
        const selectElement = form.querySelector(`select[name="status_${wohnung}"]`);
        if (selectElement) {
            selectElement.value = status;
            submitFormViaAjax(form);
        } else {
            console.error('Dropdown-Menü nicht gefunden');
        }
    } else {
        console.error('Formular nicht gefunden');
    }
}


    
    function submitStatusAsVertrag(ort, hausnummer, wohnung) {
        const form = document.querySelector(`form[data-ort="${ort}"][data-hausnummer="${hausnummer}"][data-wohnung="${wohnung}"]`);
        if (form) {
            form.dataset.autoSubmit = 'true'; 
            form.querySelector('select[name="status"]').value = 'Vertrag';
            submitFormViaAjax(form);
        } else {
            console.error('Formular nicht gefunden');
        }
    }
    
    function showStatusChanges(projectId) {
        moment.locale('de');
        const projectElement = document.querySelector(`[data-project-id="${projectId}"]`);
        const statusLogs = JSON.parse(projectElement.dataset.statusLogs);
        const modalBody = document.getElementById('statusChangesModalBody');
        
        modalBody.innerHTML = '';
    
        statusLogs.forEach(log => {
            const formattedDates = moment(log.created_at).format('LLLL');
            const statusChangeHTML = `
                <div class="mb-2">
                    <strong>Benutzer:</strong> ${log.user_id}<br>
                    <strong>Wohnungsnr :</strong> ${log.wohnung_nr}<br>
                    <strong>Alter Status:</strong> ${log.old_status}<br>
                    <strong>Neuer Status:</strong> ${log.new_status}<br>
                    <strong>Datum:</strong> ${formattedDates}<br>
                </div>
            `;
            modalBody.innerHTML += statusChangeHTML;
        });
        $('#statusChangesModal').modal('show');
    }

    function updateRowClasses() {
            $('.table tbody tr').each(function(index) {
                $(this).removeClass('even-row odd-row');
                $(this).addClass(index % 2 == 0 ? 'even-row' : 'odd-row');
            });
        }

        $('.table').on('sortEnd', function() {
            updateRowClasses();
        });

        $(document).ready(function() {
            updateRowClasses();
        });
    </script>
    
@endsection
