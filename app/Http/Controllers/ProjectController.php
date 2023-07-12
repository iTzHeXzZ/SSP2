<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Project;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Collection;



class ProjectController extends Controller
{
    public function index(){
        if (!Auth::check()) {
            return redirect()->route('login');
        }
            $projects = Project::all();
           
    
            return view('projects.index', compact('projects'));

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

        return view('projects.number', compact('projects','ort', 'postleitzahl','strasse'));
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

    public function update(Request $request,$ort, $postleitzahl, $strasse ){

        $notiz = Project::findorfail($request->notiz);

        $project->notiz = $request->input('notiz');

        $notiz->save();

        #return view('projects.number', compact('projects','ort', 'postleitzahl','strasse'));
        return redirect("test");
}
}
