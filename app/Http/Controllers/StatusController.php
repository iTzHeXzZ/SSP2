<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Project;
use App\Models\User;
use App\Models\StatusEntry;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class StatusController extends Controller
{
    public function index()
    {
        $users = User::all();
        $selectedUser = null;
    
        if (request()->has('user_id')) {
            $selectedUser = User::find(request('user_id'));
    
            // Projekte des ausgewählten Benutzers abrufen
            $projects = $selectedUser->projects;
    
            // Gruppieren Sie die Projekte nach Ort und Postleitzahl
            $groupedProjects = $projects->groupBy(function ($project) {
                return $project->ort . '-' . $project->postleitzahl;
            });
    
            return view('status.index', compact('users', 'selectedUser', 'groupedProjects'));
        }
    
        return view('status.index', compact('users', 'selectedUser'));
    }
    
    
    public function showLocation($user_id, $location)
    {
        list($ort, $postleitzahl) = explode('-', $location);
    
        $user = User::find($user_id);
    
        $streets = $user->projects
            ->where('ort', $ort)
            ->where('postleitzahl', $postleitzahl)
            ->pluck('strasse') // Hier werden nur die Straßennamen abgerufen
            ->unique(); // Entfernen Sie doppelte Straßennamen, falls vorhanden
    
        return view('status.streets', compact('user', 'ort', 'postleitzahl', 'streets'));
    }
    
    

    
    
}
