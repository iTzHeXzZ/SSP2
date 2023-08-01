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
        $projects = Project::all();
       /* if($user->hasRole('Admin')){
         $projects = Project::all();
        }
        else{
        $projects = $user->projects;
        }
        $allProjects = Project::all();*/

        return view('projects.index', compact('projects', 'allProjects'));
    } 

    public function street($ort, $postleitzahl){
        if (!Auth::check()) {
            return redirect()->route('login');
        }
        $projects = Project::where('ort', $ort)
        ->where('postleitzahl', $postleitzahl)
        ->get();

        $gwohneinheiten = new Collection();
        $gbestand = new Collection();

        foreach ($projects as $project) {
            $existingproject = $gwohneinheiten->firstWhere('strasse', $project->strasse);
    
            if ($existingproject) {
                $existingproject->wohneinheiten += $project->wohneinheiten;
                $existingproject->bestand += $project->bestand;
            } else {
                $gwohneinheiten->push($project);
                $gbestand->push($project);
            }
        }

        return view('projects.street', compact('projects','ort', 'postleitzahl','gwohneinheiten', 'gbestand'));

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

    public function update(Request $request, $id){
        $projects = Project::findorfail($id);

        $inputValue1 = $request->input('status');
        $inputValue2 = $request->input('notiz');
        if ($inputValue1 != null) {
            $projects->status = $request->input('status');
            $projects->save();
        }
        if($inputValue2 != null){
            $projects->notiz = $request->input('notiz');
            $projects->save();
        }


        return redirect()->back()->with('success', 'Aktualisiert');
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
        // Validierung der Eingabe
        $request->validate([
            'project_id' => 'required',
            'user_id' => 'required',
            'street' => 'required',
        ]);
    
        // Projekt und Benutzer finden
        $project = Project::findOrFail($request->project_id);
        $user = User::findOrFail($request->user_id);
    
        // Straße für das Projekt finden und zuweisen
        $street = $project->streets()->where('strasse', $request->street)->first();
    
        if ($street) {
            // Hausnummern für die Straße abrufen
            $hausnummern = Project::where('strasse', $request->street)->pluck('hausnummer')->toArray();
    
            // Projekt dem Benutzer zuweisen
            $user->projects()->attach($project, ['hausnummer' => implode(', ', $hausnummern)]);
        }
    
        return redirect()->route('assign.form')->with('success', 'Projekt und Straße erfolgreich zugewiesen.');
    }


    public function removeStreetFromProject(Request $request)
    {
        $user = User::find($request->user_id);
        if (!$user) {
            return back()->with('error', 'Benutzer nicht gefunden.');
        }

        $user->projects()->detach($request->project_id);

        return back()->with('success', 'Straße erfolgreich aus dem Projekt entfernt.');
    }

    public function getStreetsForLocationZipcode($ort, $postleitzahl)
    {
        $ort = $request->input('ort');
        $postleitzahl = $request->input('postleitzahl');
    
        $streets = Project::where('ort', $ort)
                        ->where('postleitzahl', $postleitzahl)
                        ->get();
    
        return response()->json($streets);
    }
}
