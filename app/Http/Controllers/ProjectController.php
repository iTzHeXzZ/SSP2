<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Project;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Collection;



class ProjectController extends Controller
{
    public function index(){
        if (!Auth::check()) {
            return redirect()->route('login');
        }
    
        $user = Auth::user();
    
        // Alle Projekte abrufen, die dem Benutzer zugeordnet sind (abhängig von seiner Rolle)
        if ($user->hasRole('Admin') || $user->hasRole('Viewer')) {
            $projects = Project::all();
        } else {
            $projects = $user->projects;
        }
    
        // Gruppieren der Projekte nach Ort und Postleitzahl und Berechnungen für jeden Ort/Postleitzahl durchführen
        $groupedProjects = $projects->groupBy(['ort', 'postleitzahl']);
        $counts = [];
    
        foreach ($groupedProjects as $key => $group) {
            $counts[$key]['countUnbesucht'] = $group->where('status', 'Unbesucht')->count();
            $counts[$key]['countVertrag'] = $group->where('status', 'Vertrag')->count();
            $counts[$key]['countOverleger'] = $group->where('status', 'Überleger')->count();
            $counts[$key]['countKarte'] = $group->where('status', 'Karte')->count();
            $counts[$key]['countKeinInteresse'] = $group->where('status', 'Kein Interesse')->count();
        }
    
        return view('projects.index', compact('projects','counts','user'));
    }
    
    

    public function street($ort, $postleitzahl){
        if (!Auth::check()) {
            return redirect()->route('login');
        }
    
        $user = Auth::user();
        $gwohneinheiten = new Collection();
        $gbestand = new Collection();
    
        $countOverleger = [];
        $countUnbesucht = [];
        $countVertrag = [];
        $countKeinInteresse = [];
        $countKarte = [];

        if ($user->hasRole('Admin') || $user->hasRole('Viewer')) {
            $projects = Project::where('ort', $ort)
            ->where('postleitzahl', $postleitzahl)
            ->get();
        } else {
            $projects = $user->projects()->where('ort', $ort)->where('postleitzahl', $postleitzahl)->get();
        }
    
        foreach ($projects as $project) {
            $existingproject = $gwohneinheiten->firstWhere('strasse', $project->strasse);
    
            if ($existingproject) {
                $existingproject->wohneinheiten += $project->wohneinheiten;
                $existingproject->bestand += $project->bestand;
            } else {
                $gwohneinheiten->push($project);
                $gbestand->push($project);
            }
            $strasse = $project->strasse;
    
            if (!isset($countOverleger[$strasse])) {
                $countOverleger[$strasse] = 0;
            }

            if (!isset($countKarte[$strasse])) {
                $countKarte[$strasse] = 0;
            }
    
            if (!isset($countUnbesucht[$strasse])) {
                $countUnbesucht[$strasse] = 0;
            }
    
            if (!isset($countVertrag[$strasse])) {
                $countVertrag[$strasse] = 0;
            }
    
            if (!isset($countKeinInteresse[$strasse])) {
                $countKeinInteresse[$strasse] = 0;
            }
    
            if ($project->status === 'Überleger') {
                $countOverleger[$strasse]++;
            } elseif ($project->status === 'Unbesucht') {
                $countUnbesucht[$strasse]++;
            } elseif ($project->status === 'Vertrag') {
                $countVertrag[$strasse]++;
            } elseif ($project->status === 'Kein Interesse') {
                $countKeinInteresse[$strasse]++;
            } elseif ($project->status === 'Karte') {
                $countKarte[$strasse]++;
            }
        }
    
        return view('projects.street', compact(
            'projects', 'ort', 'postleitzahl', 'user',
            'gwohneinheiten', 'gbestand',
            'countOverleger', 'countUnbesucht', 'countVertrag', 'countKeinInteresse','countKarte'
        ));
    }
    
    

    public function number($ort, $postleitzahl, $strasse){
        if (!Auth::check()) {
            return redirect()->route('login');
        }
        $projects = Project::where('ort', $ort)
        ->where('postleitzahl', $postleitzahl)
        ->where('strasse', $strasse)
        ->get();

        $statusOptions = ['Unbesucht', 'Kein Interesse', 'Überleger', 'Karte', 'Vertrag'];

        return view('projects.number', compact('projects','ort', 'postleitzahl','strasse','statusOptions'));
    } 

    public function create(){
        return view('projects.create');
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'ort' => 'required',
            'postleitzahl' => 'required',
            'strasse' => 'required',
            'hausnummer' => 'required',
            'wohneinheiten' => 'required',
            'bestand' => 'required',
            'bearbeitungsdatum' => 'required',
        ]);
    
        $project = Project::create($validatedData);
    
        return redirect()->route('projects.index');
    }

    public function update(Request $request, $id) {
        try {
            // Der bestehende Code für die Aktualisierung der Notiz bleibt unverändert
            $project = Project::findOrFail($id);
    
            $inputValue1 = $request->input('status');
            $inputValue2 = $request->input('notiz');
    
            $project->status = $inputValue1;
            $project->notiz = $inputValue2;
            $project->save();
    
            return response()->json(['success' => true, 'message' => 'Aktualisiert']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Fehler beim Speichern: ' . $e->getMessage()]);
        }
    }

    public function showAssignForm()
    {
        $users = User::all();
        $allUsers = User::all();
        $projectsByLocationAndZipcode = Project::all()->groupBy(function ($project) {
            return $project->ort . '_' . $project->postleitzahl;
        });

        return view('projects.assign_project', compact( 'users', 'allUsers', 'projectsByLocationAndZipcode'));
    }


        public function assignProjectToUser(Request $request)
         {
             $request->validate([
                 'project_id' => 'required',
                 'user_id' => 'required',
                 'streets' => 'required|array',
             ]);
         
             $selectedValue = $request->input('project_id');
             list($projectId) = explode('_', $selectedValue);
             $project = Project::findOrFail($projectId);
             $user = User::findOrFail($request->user_id);
     
             $selectedOrt = $project->ort;
             $selectedPostleitzahl = $project->postleitzahl;
         
             foreach ($request->streets as $streetName) {
                 if ($user->projects->contains($project) && $user->streets->contains('strasse', $streetName)) {
                     return redirect()->back()->with('error', 'Der Benutzer ist bereits diesem Projekt und dieser Straße zugewiesen.');
                 }
         
                 $street = Project::where('strasse', $streetName)
                     ->where('ort', $selectedOrt) // Hinzugefügt: Filtere nach dem ausgewählten Ort
                     ->where('postleitzahl', $selectedPostleitzahl) // Hinzugefügt: Filtere nach der ausgewählten Postleitzahl
                     ->first();
         
                 if (!$street) {
                     return redirect()->back()->with('error', 'Die ausgewählte Straße konnte nicht gefunden werden.');
                 }
         
                 // Hinzugefügt: Filtere andere Projekte nach dem ausgewählten Ort und Postleitzahl
                 $otherProjects = Project::where('strasse', $streetName)
                     ->where('ort', $selectedOrt)
                     ->where('postleitzahl', $selectedPostleitzahl)
                     ->where('id', '!=', $projectId)
                     ->get();
         
                 if ($otherProjects->isNotEmpty()) {
                     foreach ($otherProjects as $otherProject) {
                         $user->projects()->attach($otherProject);
                     }
                 }
             }
             return redirect()->route('assign.form')->with('success', 'Projekt und Straßen erfolgreich zugewiesen.');
    }
    
    
    
    


    public function removeStreetFromProject(Request $request)
    {
        $user = User::find($request->user_id);
        if (!$user) {
            return back()->with('error', 'Benutzer nicht gefunden.');
        }
    
        // Hier nehmen wir an, dass $request->strasse der Name der Straße ist.
        
        // Finden Sie alle Projekte mit dieser Straße.
        $projects = Project::where('strasse', $request->strasse)->get();
    
        if ($projects->isEmpty()) {
            return back()->with('error', 'Projekte mit dieser Straße konnten nicht gefunden werden.');
        }
    
        // Entfernen Sie die Beziehungen zwischen Benutzer und allen gefundenen Projekten (Straßen).
        foreach ($projects as $project) {
            $user->projects()->detach($project->id);
        }
    
        return back()->with('success', 'Straße erfolgreich aus den Projekten entfernt.');
    }
    
    
    
    

    public function getStreetsForLocationZipcode($ort, $postleitzahl)
    {
    
        $streets = Project::where('ort', $ort)
                        ->where('postleitzahl', $postleitzahl)
                        ->get();
    
        return response()->json($streets);
    }
}
