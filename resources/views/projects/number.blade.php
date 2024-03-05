@extends('layouts.app')

@section('content')
    <h2>{{$postleitzahl}},<a href="{{ route('projects.street', ['ort' => $ort, 'postleitzahl' => $postleitzahl]) }}" style="text-decoration : none">{{ $ort }}</a>,{{ $strasse }} Hausnummer:</h2>

    <table class="table">
        <thead>
            <tr>
                <th data-sort="0">Hausnummer</th>
                <th data-sort="1">Status</th>
                <th data-sort="2">Wohneinheiten</th>
                <th data-sort="3">Bestand</th>
                <th data-sort="4">Bearbeitungsdatum</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($projects as $project)
                <tr>
                    <td>{{ $project->hausnummer }}</td>
                    <td>
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

.feedback-message.success {
    background-color: #28a745; /* Grüner Hintergrund für Erfolgsmeldung */
}

.feedback-message.error {
    background-color: #dc3545; /* Roter Hintergrund für Fehlermeldung */
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
                        
                        <form id="projectForm_{{ $project->id }}" class="ajax-form" data-ort="{{ $ort }}" data-hausnummer="{{ $project->hausnummer }}">
                            @csrf
                            <select name="status" onchange="handleVertragSelect(this, '{{ $ort }}', '{{ $project->hausnummer }}')">
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
                                    <a href="#" class="btn" onclick="selectAndSave('Karte', '{{ $ort }}', '{{ $project->hausnummer }}')" style="display: inline-block; margin-left: 5px; vertical-align: middle;">
                                        <img src="/Images/visitenkarte.png" alt="Visitenkarte Symbol" style="width: 35px; height: 30px;">
                                    </a>                                    
                                </div>                                                                             
                            @endif
                        </form>
                    </td>
                    <td>{{ $project->wohneinheiten }}</td>
                    <td>{{ $project->bestand }} </td>
                    <td>{{ $project->updated_at }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
@endsection

@section('scripts')
<script>
function submitFormViaAjax(form) {
    event.preventDefault();
    const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

    // Setzen des CSRF-Tokens für alle AJAX-Anfragen
    axios.defaults.headers.common['X-CSRF-TOKEN'] = csrfToken;

    // Formulardaten sammeln
    const formData = new FormData(form);

    // Extrahiere die Projekt-ID aus dem Formular-ID-Attribut
    const projectId = form.id.split('_')[1];


    axios.post(`/projects/update/${projectId}`, formData)
        .then(response => {
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

function selectAndSave(status, ort, hausnummer) {
    // Sucht das zugehörige Formular zum Dropdown
    const form = document.querySelector(`form[data-ort="${ort}"][data-hausnummer="${hausnummer}"]`);

    if (form) {
        // Setzt den Dropdown-Wert auf "Karte"
        form.querySelector('select[name="status"]').value = status;

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



function handleVertragSelect(selectElement, ort, hausnummer) {
    var selectedValue = selectElement.value;

    if (selectedValue === 'Vertrag' && ort.includes('SWLangenfeld')) {
        var popup = window.open('https://www.stw-langenfeld.de/media/glasfaser-ausbaugebiete-langenfeld/', '_blank');

        var interval = setInterval(function () {
            if (popup.closed) {
                clearInterval(interval);
                alert('Vertrag erfolgreich erstellt');
                selectElement.form.elements["status"].value = selectedValue;
                selectElement.form.submit();
                window.open("/pdf/showForm", "_blank");
            }
        }, 1000);  
    }
}
</script>
@endsection
