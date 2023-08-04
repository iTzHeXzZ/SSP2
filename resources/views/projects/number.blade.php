@extends('layouts.app')


@section('content')
<h2>{{$postleitzahl}},<a href="{{ route('projects.street', ['ort' => $ort, 'postleitzahl' => $postleitzahl]) }}" style="text-decoration : none">{{ $ort }}</a>,{{ $strasse }} Hausnummer:</h2>

    <table class="table">
        <thead>
            <tr>
                <th data-sort="0">Hausnummer</th>
                <th data-sort="1">Wohneinheiten</th>
                <th data-sort="2">Bestand</th>
                <th data-sort="3">Status</th>
                <th data-sort="4">Notiz</th>
                <th data-sort="5">Bearbeitungsdatum</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($projects as $project)
                <tr>
                    <td>{{ $project->hausnummer }}</td>
                    <td>{{ $project->wohneinheiten }}</td>
                    <td>{{ $project->bestand }} </td>
                    <td>
                        <form method="POST" action="{{ route('projects.update', $project->id)}}">
                         @csrf
                         <select name="status">
                            @foreach ($statusOptions as $option)
                                <option value="{{ $option }}" {{ $project->status === $option ? 'selected' : '' }}>
                                    {{ $option }}
                                </option>
                            @endforeach
                        </select>
                        <button type="submit" class="btn btn-primary">Speichern</button>
                        </form>
                    </td>
                    <td>
                        <form method="POST" action="{{ route('projects.update', $project->id)}}">
                            @csrf
                            <input type="text" name="notiz" value="{{ $project->notiz }}">
                            <button type="submit" class="btn btn-primary">Speichern</button>
                        </form>
                        </td>
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