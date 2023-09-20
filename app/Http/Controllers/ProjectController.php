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
        
        if ($user->hasRole('Admin')) {
            $projects = Project::all();
        } else {
            $projects = $user->projects;
        }

        return view('projects.index', compact('projects','user'));
    } 

    public function street($ort, $postleitzahl){
        if (!Auth::check()) {
            return redirect()->route('login');
        }
        $projects = Project::where('ort', $ort)
        ->where('postleitzahl', $postleitzahl)
        ->get();
        $user = Auth::user();
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

        return view('projects.street', compact('projects','ort', 'postleitzahl','gwohneinheiten', 'gbestand','user'));

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
        $project = Project::findOrFail($id);
    
        $inputValue1 = $request->input('status');
        $inputValue2 = $request->input('notiz');
    
        $project->status = $inputValue1;
        $project->notiz = $inputValue2;
        $project->save();
    
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
        $request->validate([
            'project_id' => 'required',
            'user_id' => 'required',
            'street' => 'required',
        ]);
    
        $selectedValue = $request->input('project_id');
        list($projectId) = explode('_', $selectedValue);
        $project = Project::findOrFail($projectId);
        $user = User::findOrFail($request->user_id);
    
        if ($user->projects->contains($project) && $user->streets->contains('strasse', $request->street)) {
            return redirect()->back()->with('error', 'Der Benutzer ist bereits diesem Projekt und dieser Straße zugewiesen.');
        }
    
        $street = Project::where('strasse', $request->street)->first();
    
        if (!$street) {
            return redirect()->back()->with('error', 'Die ausgewählte Straße konnte nicht gefunden werden.');
        }
    
        $otherProjects = Project::where('strasse', $request->street)
            ->where('id', '!=', $project->id)
            ->get();
    
        //$user->projects()->attach($project);
    
        if ($otherProjects->isNotEmpty()) {
            foreach ($otherProjects as $otherProject) {
                $user->projects()->attach($otherProject);
            }
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
    
        $streets = Project::where('ort', $ort)
                        ->where('postleitzahl', $postleitzahl)
                        ->get();
    
        return response()->json($streets);
    }
}
