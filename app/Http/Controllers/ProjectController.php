<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Project;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Collection;
use App\Models\ProjectStatusLog;
use Illuminate\Support\Facades\Log;
use App\Imports\ProjectsImport;
use Maatwebsite\Excel\Facades\Excel;





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
    
    

    public function street($ort, $postleitzahl)
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }
    
        $user = Auth::user();
        $gwohneinheiten = new Collection();
        $gbestand = new Collection();
    
        // Initialize counters as arrays
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
    
            // Initialize counters if not set
            $countOverleger[$strasse] ??= 0;
            $countUnbesucht[$strasse] ??= 0;
            $countVertrag[$strasse] ??= 0;
            $countKeinInteresse[$strasse] ??= 0;
            $countKarte[$strasse] ??= 0;
    
            // Update counters based on project status
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
            'countOverleger', 'countUnbesucht', 'countVertrag', 'countKeinInteresse', 'countKarte'
        ));
    }
    
    
    

    public function number($ort, $postleitzahl, $strasse){
        if (!Auth::check()) {
            return redirect()->route('login');
        }
        $userId = Auth::id();
        $projects = Project::where('ort', $ort)
        ->where('postleitzahl', $postleitzahl)
        ->where('strasse', $strasse)
        ->get();

        $statusOptions = ['Unbesucht', 'Kein Interesse', 'Überleger', 'Karte', 'Vertrag'];

        return view('projects.number', compact('projects','ort', 'postleitzahl','strasse','statusOptions', 'userId'));
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
            $oldStatus = $project->status;
            $inputValue1 = $request->input('status');
            $inputValue2 = $request->input('notiz');
    
            $project->status = $inputValue1;
            $project->notiz = $inputValue2;
            $project->save();
            $project->touch();

                ProjectStatusLog::create([
                    'project_id' => $project->id,
                    'user_id' => Auth::id(),
                    'old_status' => $oldStatus,
                    'new_status' => $inputValue1
                ]);
    
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
                //  if ($user->projects->contains($project) && $user->streets->contains('strasse', $streetName)) {
                //      return redirect()->back()->with('error', 'Der Benutzer ist bereits diesem Projekt und dieser Straße zugewiesen.');
                //  }
         
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
    
        $selectedStreets = $request->streets;
    
        if (empty($selectedStreets)) {
            return back()->with('error', 'Keine Straßen ausgewählt.');
        }
    
        foreach ($selectedStreets as $selectedStreet) {
            list($selectedStrasse, $selectedOrt) = explode(', ', $selectedStreet);
    
            $projects = Project::where('strasse', $selectedStrasse)
                               ->where('ort', $selectedOrt)
                               ->get();
    
            if ($projects->isEmpty()) {
                return back()->with('error', 'Projekte mit dieser Straße und diesem Ort konnten nicht gefunden werden.');
            }
    
            foreach ($projects as $project) {
                $user->projects()->detach($project->id);
            }
        }
    
        return back()->with('success', 'Ausgewählte Straßen erfolgreich aus den Projekten entfernt.');
    }
    
    
    
    
    
    
    
    

    public function getStreetsForLocationZipcode($ort, $postleitzahl)
    {
    
        $streets = Project::where('ort', $ort)
                        ->where('postleitzahl', $postleitzahl)
                        ->get();
    
        return response()->json($streets);
    }


    public function getStreetsForUser($userId)
    {
        $user = User::findOrFail($userId);
        $projects = $user->projects;
    
        $streetsAndOrte = $projects->map(function ($project) {
            return [
                'strasse' => $project->strasse,
                'ort' => $project->ort,
            ];
        });
    
        return response()->json(['streetsAndOrte' => $streetsAndOrte]);
    }

    public function getProjectChangeLogs($projectId)
        {
            $logs = ProjectStatusLog::where('project_id', $projectId)->get();
            return view('projects.logs', ['logs' => $logs]);
        }

        public function showMonthlyAnalysis(Request $request)
        {
            $startDate = $request->input('start_date', now()->startOfMonth()->toDateString());
            $endDate = $request->input('end_date', now()->endOfMonth()->toDateString());
        
            $latestStatusLogs = ProjectStatusLog::selectRaw('MAX(id) as id')
                                                ->whereBetween('created_at', [$startDate, $endDate])
                                                ->groupBy('project_id');
        
            $logs = ProjectStatusLog::with('user', 'project')
                                    ->whereIn('id', $latestStatusLogs)
                                    ->get()
                                    ->groupBy('user_id')
                                    ->mapWithKeys(function ($entries, $userId) {
                                        return [$userId => $entries->groupBy('new_status')->map(function ($statusEntries) {
                                            return $statusEntries->count();
                                        })];
                                    });
        
            if (!Auth::user()->hasRole('Admin')) {
                $logs = $logs->filter(function ($data, $userId) {
                    return $userId == Auth::id();
                });
            }
        
            $users = User::whereIn('id', $logs->keys())->get();
        
            return view('projects.analyse', [
                'stats' => $logs,
                'users' => $users
            ]);
        }
        
        
        

        public function getProjectDetails($userId, $status, Request $request)
        {
            $startDate = $request->query('start_date', now()->startOfMonth()->toDateString());
            $endDate = $request->query('end_date', now()->endOfMonth()->toDateString());
           
            $latestLogs = ProjectStatusLog::selectRaw('MAX(id) as id')
                                          ->whereBetween('created_at', [$startDate, $endDate])
                                          ->groupBy('project_id');
        
            $projects = ProjectStatusLog::whereIn('id', $latestLogs)
                                        ->where('user_id', $userId)
                                        ->where('new_status', $status)
                                        ->with('project')
                                        ->paginate(10);

                                        Log::info('Pagination Details', [
                                            'total' => $projects->total(),
                                            'current_page' => $projects->currentPage(),
                                            'last_page' => $projects->lastPage(),
                                            'next_page_url' => $projects->nextPageUrl(),
                                            'prev_page_url' => $projects->previousPageUrl()
                                        ]);
        
            return response()->json([
                'data' => $projects->getCollection()->transform(function ($log) {
                    return $log->project ? [
                        'ort' => $log->project->ort,
                        'postleitzahl' => $log->project->postleitzahl,
                        'strasse' => $log->project->strasse,
                        'hausnummer' => $log->project->hausnummer,
                        'status' => $log->new_status  
                    ] : null;
                })->filter(),
                'pagination' => [
                    'total' => $projects->total(),
                    'count' => $projects->count(),
                    'per_page' => $projects->perPage(),
                    'current_page' => $projects->currentPage(),
                    'total_pages' => $projects->lastPage(),
                    'links' => [
                        'next' => $projects->nextPageUrl(),
                        'prev' => $projects->previousPageUrl()
                        
                    ]
                ]
            ]);
        }
        
        public function showImportForm()
        {
            return view('import');
        }
    
        public function import(Request $request)
        {
            if ($request->hasFile('file')) {
                $file = $request->file('file');
        
                try {
                    Excel::import(new ProjectsImport, $file);
                    return redirect()->back()->with('success', 'Projekt erfolgreich importiert!');
                } catch (\Exception $e) {
                    return redirect()->back()->with('error', 'Fehler: ' . $e->getMessage());
                }
            }
            return redirect()->back()->with('error', 'No file selected!');
        }
    
        public function destroyProject($ort, $postleitzahl)
        {   
            $user = Auth::user();
            
            if ($user->hasRole('Admin')) {
                Project::where('ort', $ort)->where('postleitzahl', $postleitzahl)->delete();
                return redirect()->back()->with('success', 'Projekte erfolgreich gelöscht.');
            }

            
            abort(403, 'Nur Administratoren dürfen Projekte löschen.');

            

        }
}
