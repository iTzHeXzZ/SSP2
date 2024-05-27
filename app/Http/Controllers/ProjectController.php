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
use App\Exports\ProjectsExport;
use App\Models\SubProject;
use App\Models\CompletedContract;
use Carbon\Carbon;
use App\Models\ArchivedProject;
use DB;






class ProjectController extends Controller
{
    public function index(Request $request)
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }
    
        $user = Auth::user();
        $search = $request->get('search');
    
        if ($user->hasRole('Admin') || $user->hasRole('Viewer')) {
            $projects = Project::with('subProjects')
                ->when($search, function ($query, $search) {
                    return $query->where('ort', 'like', '%' . $search . '%')
                                 ->orWhere('postleitzahl', 'like', '%' . $search . '%');
                })->get();
        } else {
            $projects = $user->projects()->with('subProjects')
                ->when($search, function ($query, $search) {
                    return $query->where('ort', 'like', '%' . $search . '%')
                                 ->orWhere('postleitzahl', 'like', '%' . $search . '%');
                })->get();
        }
    
        $counts = [];
    
        foreach ($projects as $project) {
            $groupedSubProjects = $project->subProjects->groupBy('status');
    
            $counts[$project->id] = [
                'Unbesucht' => 0,
                'Vertrag' => 0,
                'Überleger' => 0,
                'Karte' => 0,
                'Kein Interesse' => 0,
                'Kein Potenzial' => 0
            ];
    
            foreach ($groupedSubProjects as $status => $subProjects) {
                $counts[$project->id][$status] = $subProjects->count();
            }
        }
    
        return view('projects.index', compact('projects', 'counts', 'user', 'search'));
    }
    

    public function archivedIndex()
    {
        $archivedProjects = ArchivedProject::all();
        
        $groupedProjects = $archivedProjects->groupBy(['ort', 'postleitzahl']);
        $counts = [];
    
        foreach ($groupedProjects as $key => $group) {
            $counts[$key]['countUnbesucht'] = $group->where('status', 'Unbesucht')->count();
            $counts[$key]['countVertrag'] = $group->where('status', 'Vertrag')->count();
            $counts[$key]['countOverleger'] = $group->where('status', 'Überleger')->count();
            $counts[$key]['countKarte'] = $group->where('status', 'Karte')->count();
            $counts[$key]['countKeinInteresse'] = $group->where('status', 'Kein Interesse')->count();
        }
    
        return view('projects.index', compact('archivedProjects','counts'));
    }
    
    

    
    
    

    public function street($ort, $postleitzahl)
    {
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
        $countFremdVP = [];
        $countKeinPotenzial = [];
    
        $projectsQuery = Project::with('subProjects');

        // Check if user is admin or viewer
        if ($user->hasRole('Admin') || $user->hasRole('Viewer')) {
            $projectsQuery->where('ort', $ort)->where('postleitzahl', $postleitzahl);
        } else {
            $projectsQuery->whereHas('users', function($query) use ($user, $ort, $postleitzahl) {
                $query->where('user_id', $user->id)
                      ->where('ort', $ort)
                      ->where('postleitzahl', $postleitzahl);
            });
        }
    
        // Get projects
        $projects = $projectsQuery->get();
    
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
            $countFremdVP[$strasse] ??= 0;
            $countKeinPotenzial[$strasse] ??= 0;
    
            // Update counters based on project status
            switch ($project->status) {
                case 'Überleger':
                    $countOverleger[$strasse]++;
                    break;
                case 'Unbesucht':
                    $countUnbesucht[$strasse]++;
                    break;
                case 'Vertrag':
                    $countVertrag[$strasse]++;
                    break;
                case 'Kein Interesse':
                    $countKeinInteresse[$strasse]++;
                    break;
                case 'Karte':
                    $countKarte[$strasse]++;
                    break;
                case 'Fremd VP':
                    $countFremdVP[$strasse]++;
                    break;
                case 'Kein Potenzial':
                    $countKeinPotenzial[$strasse]++;
                    break;
            }
    
            // Include subproject counts
            foreach ($project->subProjects as $subProject) {
                // Check if the subproject belongs to the current project
                if ($subProject->project_id === $project->id) {
                    $subProjectStatus = $subProject->status;
    
                    // Update counters based on subproject status
                    switch ($subProjectStatus) {
                        case 'Überleger':
                            $countOverleger[$strasse]++;
                            break;
                        case 'Unbesucht':
                            $countUnbesucht[$strasse]++;
                            break;
                        case 'Vertrag':
                            $countVertrag[$strasse]++;
                            break;
                        case 'Kein Interesse':
                            $countKeinInteresse[$strasse]++;
                            break;
                        case 'Karte':
                            $countKarte[$strasse]++;
                            break;
                        case 'Fremd VP':
                            $countFremdVP[$strasse]++;
                            break;
                        case 'Kein Potenzial':
                            $countKeinPotenzial[$strasse]++;
                            break;
                    }
                }
            }
        }
    
        return view('projects.street', compact(
            'projects', 'ort', 'postleitzahl', 'user',
            'gwohneinheiten', 'gbestand',
            'countOverleger', 'countUnbesucht', 'countVertrag', 'countKeinInteresse', 'countKeinPotenzial', 'countKarte', 'countFremdVP'
        ));
    }
    
    
    
    
    

    public function number($ort, $postleitzahl, $strasse){
        if (!Auth::check()) {
            return redirect()->route('login');
        }
        $userId = Auth::id();
        $projects = Project::with('statusLogs')->where('ort', $ort)
        ->where('postleitzahl', $postleitzahl)
        ->where('strasse', $strasse)
        ->get();

        $statusOptions = ['Unbesucht', 'Kein Interesse', 'Überleger', 'Karte', 'Vertrag', 'Fremd VP', 'Kein Potenzial'];
        $projects->each(function ($project) {
            $project->wohneinheiten = max($project->wohneinheiten, 0);
            $project->subProjects = range(1, $project->wohneinheiten);
        });
    

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
            $project = Project::findOrFail($id);
            $oldStatus = $project->status;
    
            // Wenn Daten für das Hauptprojekt vorhanden sind
            if ($request->filled('status')) {
                $inputValue1 = $request->input('status');
                $inputValue2 = $request->input('notiz');
    
                $project->status = $inputValue1;
                $project->notiz = $inputValue2;
                $project->save();
                $project->touch();
    
                // Projektstatus-Log für das Hauptprojekt erstellen
                ProjectStatusLog::create([
                    'project_id' => $project->id,
                    'user_id' => Auth::id(),
                    'old_status' => $oldStatus,
                    'new_status' => $inputValue1,
                    'wohnung_nr' => 1
                ]);
            }
    

    for ($i = 2; $i <= $project->wohneinheiten; $i++) {
        if ($request->filled("status_$i")) {
            $subProjectStatus = $request->input("status_$i", 'Unbesucht');
            $subProjectNotiz = $request->input("notiz_$i", '');

            $subProject = SubProject::updateOrCreate(
                ['project_id' => $project->id, 'wohnung_nr' => $i],
                ['status' => $subProjectStatus, 'notiz' => $subProjectNotiz]
            );

            // Erstelle den Projektstatus-Log für das Subprojekt
            ProjectStatusLog::create([
                'project_id' => $project->id,
                'user_id' => Auth::id(),
                'old_status' => $oldStatus,
                'new_status' => $subProjectStatus,
                'wohnung_nr' => $i
            ]);
        }
    }
    
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
                     ->where('ort', $selectedOrt)
                     ->where('postleitzahl', $selectedPostleitzahl) 
                     ->first();
         
                 if (!$street) {
                     return redirect()->back()->with('error', 'Die ausgewählte Straße konnte nicht gefunden werden.');
                 }
         
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
        $endDate = $request->query('end_date');

        if ($endDate) {
            $endDate = Carbon::parse($endDate)->addDay()->toDateString();
        } else {
            $endDate = Carbon::now()->endOfMonth()->addDay()->toDateString();
        }
    
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
    
        $user = Auth::user();
        $users = $user->hasRole('Admin') ? User::all() : [$user];
    
        $unbesuchteCounts = [];
    
        foreach ($users as $currentUser) {
            $unbesuchteCount = $currentUser->projects()->where('status', 'Unbesucht')->count();
            $unbesuchteCounts[$currentUser->id] = $unbesuchteCount;
        }
    
        return view('projects.analyse', [
            'stats' => $logs,
            'users' => $users,
            'unbesuchte_counts' => $unbesuchteCounts 
        ]);
    }
    
        
        
        

        public function getProjectDetails($userId, $status, Request $request)
        {
            $startDate = $request->query('start_date', now()->startOfMonth()->toDateString());
            $endDate = $request->query('end_date');

            if ($endDate) {
                $endDate = Carbon::parse($endDate)->addDay()->toDateString();
            } else {
                $endDate = Carbon::now()->endOfMonth()->addDay()->toDateString();
            }

            $latestLogs = ProjectStatusLog::selectRaw('MAX(id) as id')
                                          ->whereBetween('created_at', [$startDate, $endDate])
                                          ->groupBy('project_id');
            
            $projects = ProjectStatusLog::whereIn('id', $latestLogs)
                                        ->where('user_id', $userId)
                                        ->where('new_status', $status)
                                        ->with('project')
                                        ->paginate(10);
        
        
                                        $unbesuchtCount = Project::whereHas('users', function($query) use ($userId) {
                                            $query->where('user_id', $userId);
                                        })
                                        ->where('status', 'Unbesucht')
                                        ->count();
        
            $data = $projects->getCollection()->transform(function ($log) {
                $subProjectCount = SubProject::where('project_id', $log->project_id)
                                              ->where('status', $log->new_status)
                                              ->count();
                
                return $log->project ? [
                    'ort' => $log->project->ort,
                    'postleitzahl' => $log->project->postleitzahl,
                    'strasse' => $log->project->strasse,
                    'hausnummer' => $log->project->hausnummer,
                    'status' => $log->new_status,
                    'sub_projects_count' => $subProjectCount  ?? 0
                ] : null;
            })->filter();
        
            return response()->json([
                'data' => $data,
                'unbesucht_count' => $unbesuchtCount,
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
        

        public function show(Request $request)
        {
            $user = auth()->user();
            if ($user->hasRole('Admin')) {
                $users = User::whereHas('auftraege')->get();
            } else {
                $users = collect([$user]);
            }
            $startDate = $request->input('start_date', now()->startOfMonth()->toDateString());
            $endDate = $request->query('end_date');

            if ($endDate) {
                $endDate = Carbon::parse($endDate)->addDay()->toDateString();
            } else {
                $endDate = Carbon::now()->endOfMonth()->addDay()->toDateString();
            }

            $packageNames = ['gf1000', 'gf600', 'gf300', 'gf15024m', 'gf15012m', 'fritzbox', 'firstflat', 'UGG100', 'UGG250', 'UGG500', 'UGG1000'];
            $packageCounts = [];
        
            foreach ($users as $user) {
                $userPackageCounts = [];
        
                foreach ($packageNames as $packageName) {
                    if ($packageName === 'fritzbox' || $packageName === 'firstflat') {
                        $packageCount = $user->auftraege()->where($packageName, 1)
                                          ->whereBetween('created_at', [$startDate, $endDate])
                                          ->count();
                    } elseif (strpos($packageName, 'gf') === 0|| strpos($packageName, 'UGG') === 0) {
                        $packageCount = $user->auftraege()->where('gfpaket', $packageName)
                                          ->whereBetween('created_at', [$startDate, $endDate])
                                          ->count();
                    } else {
                        $packageCount = $user->auftraege()->where($packageName, $packageName)
                                          ->whereBetween('created_at', [$startDate, $endDate])
                                          ->count();
                    }
                    $userPackageCounts[$packageName] = $packageCount;
                }
        
                $packageCounts[$user->id] = $userPackageCounts;
            }
        
            return view('auswertung', [
                'users' => $users,
                'packageNames' => $packageNames,
                'packageCounts' => $packageCounts,
                'customColumnNames' => [ 'GF1000', 'GF600', 'GF300', 'GF150 24M', 'GF150 12M', 'Fritzbox', 'Flatrate', 'UGG100', 'UGG250', 'UGG500', 'UGG1000'],
            ]);
        }


        public function getUserAndOrderData(Request $request)
        {
            $user = auth()->user();
            if ($user->hasRole('Admin')) {
                $users = User::whereHas('auftraege')->get();
            } else {
                $users = collect([$user]);
            }
            $startDate = $request->input('start_date', now()->startOfMonth()->toDateString());
            $endDate = $request->query('end_date');

            if ($endDate) {
                $endDate = Carbon::parse($endDate)->addDay()->toDateString();
            } else {
                $endDate = Carbon::now()->endOfMonth()->addDay()->toDateString();
            }

            $packageNames = ['gf1000', 'gf600', 'gf300', 'gf15024m', 'gf15012m', 'fritzbox', 'firstflat', 'UGG100', 'UGG250', 'UGG500', 'UGG1000'];
            $packageCounts = [];
        
            foreach ($users as $user) {
                $userPackageCounts = [];
        
                foreach ($packageNames as $packageName) {
                    if ($packageName === 'fritzbox' || $packageName === 'firstflat') {
                        $packageCount = $user->auftraege()->where($packageName, 1)
                                          ->whereBetween('created_at', [$startDate, $endDate])
                                          ->count();
                    } elseif (strpos($packageName, 'gf') === 0 || strpos($packageName, 'UGG') === 0) {
                        $packageCount = $user->auftraege()->where('gfpaket', $packageName)
                                          ->whereBetween('created_at', [$startDate, $endDate])
                                          ->count();
                    } else {
                        $packageCount = $user->auftraege()->where($packageName, $packageName)
                                          ->whereBetween('created_at', [$startDate, $endDate])
                                          ->count();
                    }
                    $userPackageCounts[$packageName] = $packageCount;
                }
        
                $packageCounts[$user->id] = $userPackageCounts;
            }
            return response()->json([
                'users' => $users,
                'packageNames' => $packageNames,
                'packageCounts' => $packageCounts,
                'customColumnNames' => ['GF1000', 'GF600', 'GF300', 'GF150 24M', 'GF150 12M', 'Fritzbox', 'Flatrate', 'UGG100', 'UGG250', 'UGG500', 'UGG1000'],
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



        public function showArchivedProjects()
        {
            $archivedProjects = ArchivedProject::all();
        
            $groupedProjects = $archivedProjects->groupBy(['ort', 'postleitzahl']);
            $counts = [];
        
            foreach ($groupedProjects as $key => $group) {
                $counts[$key]['countUnbesucht'] = $group->where('status', 'Unbesucht')->count();
                $counts[$key]['countVertrag'] = $group->where('status', 'Vertrag')->count();
                $counts[$key]['countOverleger'] = $group->where('status', 'Überleger')->count();
                $counts[$key]['countKarte'] = $group->where('status', 'Karte')->count();
                $counts[$key]['countKeinInteresse'] = $group->where('status', 'Kein Interesse')->count();
            }
        
            return view('archived_projects', compact('archivedProjects','counts'));
        }
        



        public function archiveProject($ort, $postleitzahl)
        {
            $user = Auth::user();
        
            if ($user->hasRole('Admin')) {
                $projects = Project::where('ort', $ort)->where('postleitzahl', $postleitzahl)->get();
        
                foreach ($projects as $project) {
                    $archivedProject = new ArchivedProject();
                    $archivedProject->id = $project->id;
                    $archivedProject->ort = $project->ort;
                    $archivedProject->postleitzahl = $project->postleitzahl;
                    $archivedProject->strasse = $project->strasse;
                    $archivedProject->hausnummer = $project->hausnummer;
                    $archivedProject->wohneinheiten = $project->wohneinheiten;
                    $archivedProject->bestand = $project->bestand;
                    $archivedProject->notiz = $project->notiz;
                    $archivedProject->status = $project->status;
                    $archivedProject->created_at = $project->created_at;
                    $archivedProject->updated_at = $project->updated_at;
                    $archivedProject->save();
                    $project->delete();
                }
        
                return redirect()->back()->with('success', 'Projekte erfolgreich archiviert.');
            }
            
            abort(403, 'Nur Administratoren dürfen Projekte archivieren.');
        }

        public function restoreProject($ort, $postleitzahl)
        {
            $user = Auth::user();
        
            if ($user->hasRole('Admin')) {
                $archivedProjects = ArchivedProject::where('ort', $ort)->where('postleitzahl', $postleitzahl)->get();
        
                foreach ($archivedProjects as $archivedProject) {
                    $project = new Project();
                    $project->id = $archivedProject->id;
                    $project->ort = $archivedProject->ort;
                    $project->postleitzahl = $archivedProject->postleitzahl;
                    $project->strasse = $archivedProject->strasse;
                    $project->hausnummer = $archivedProject->hausnummer;
                    $project->wohneinheiten = $archivedProject->wohneinheiten;
                    $project->bestand = $archivedProject->bestand;
                    $project->notiz = $archivedProject->notiz;
                    $project->status = $archivedProject->status;
                    $project->created_at = $archivedProject->created_at;
                    $project->updated_at = $archivedProject->updated_at;
        
                    $project->save();
                    $archivedProject->delete();
                }
        
                return redirect()->back()->with('success', 'Projekte erfolgreich wiederhergestellt.');
            }
            
            abort(403, 'Nur Administratoren dürfen Projekte wiederherstellen.');
        }
        
        public function exportExcel(Request $request)
        {
            $ort = $request->ort;
            $postleitzahl = $request->postleitzahl;
            
            $name = $ort . ',' . $postleitzahl;

            $projects = Project::where('ort', $ort)
                               ->where('postleitzahl', $postleitzahl)
                               ->get();
            
            return Excel::download(new ProjectsExport($projects), $name . '.xlsx');
        }
        
        public function getProjectAnalysis($userId)
        {
            $user = User::findOrFail($userId);
            $projects = $user->projects()->with('subProjects')->get();
            
            $counts = [];
        
            foreach ($projects as $project) {
                $status = $project->status;
                $counts[$status] = isset($counts[$status]) ? $counts[$status] + 1 : 1;
        
                foreach ($project->subProjects as $subProject) {
                    $subStatus = $subProject->status;
                    $counts[$subStatus] = isset($counts[$subStatus]) ? $counts[$subStatus] + 1 : 1;
                }
            }
        
            return response()->json($counts);
        }
        
        
}
