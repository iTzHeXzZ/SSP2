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

                                /* Media Query für mobile Ansicht */
                                @media (max-width: 768px) {
                                select,
                                textarea {
                                    padding: 10px;
                                    font-size: inherit;
                                    width: calc(100% + 50px); /* Breite um 20px erhöhen */
                                    margin-left: -10px; /* Negative Margin, um zur rechten Seite auszudehnen */
                                    margin-right: -10px;
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