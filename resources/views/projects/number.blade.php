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
                            /* Stil für das Formular */
                            form {
                                display: flex;
                                flex-direction: column;
                                align-items: flex-start;
                                padding: 15px; /* Abstand zum Inhalt */
                                background-color: #f5f5f5; /* Angepasste Hintergrundfarbe */
                                border-radius: 4px;
                                box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
                            }
                        
                            /* Stil für das Select-Feld und das Textarea-Feld */
                            select,
                            textarea {
                                padding: 8px;
                                margin-bottom: 10px;
                                border: 1px solid #ccc;
                                border-radius: 4px;
                                width: 100%;
                                background-color: white; /* Weißer Hintergrund für die Felder */
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
                        
                            /* Media Query für mobile Ansicht */
                            @media (max-width: 768px) {
                                select,
                                textarea {
                                    width: 100%;
                                    padding: 10px;
                                    font-size: 14px;
                                }
                            }
                        </style>
                        
                        
                        <form method="POST" action="{{ route('projects.update', $project->id)}}">
                            @csrf
                            <select name="status">
                                @foreach ($statusOptions as $option)
                                    <option value="{{ $option }}" {{ $project->status === $option ? 'selected' : '' }}>
                                        {{ $option }}
                                    </option>
                                @endforeach
                            </select>
                            <textarea name="notiz">{{ $project->notiz }}</textarea>
                            <button type="submit" class="btn btn-primary">Speichern</button>
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
// Speichere die aktuelle Scroll-Position, bevor du die Seite verlässt
window.addEventListener('beforeunload', () => {
  const scrollPosition = window.scrollY;
  localStorage.setItem('scrollPosition', scrollPosition);
});

// Rufe die gespeicherte Scroll-Position ab und scrolle zur Position zurück
const scrollPosition = localStorage.getItem('scrollPosition');
if (scrollPosition !== null) {
  window.scrollTo(0, scrollPosition);
  localStorage.removeItem('scrollPosition');
}
</script>
@endsection