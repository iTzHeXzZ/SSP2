<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    public $timestamps = true;
    protected $fillable = ['id','ort','postleitzahl','strasse','hausnummer','wohneinheiten','bestand','notiz', 'status','created_at','updatet_at'];
    
    public function statusLogs()
    {
        return $this->hasMany(ProjectStatusLog::class, 'project_id');
    }
    public function users()
    {
        return $this->belongsToMany(User::class)
        ->withPivot('hausnummer');
    }

    public function strasse()
    {
        return $this->hasMany(Project::class, 'strasse');
    }

    public function countUnbesucht()
    {
        return $this->where('status', 'Unbesucht')->count();
    }

    public function countOverleger()
    {
        return $this->where('status', 'Überleger')->count();
    }

    public function countVertrag()
    {
        return $this->where('status', 'Vertrag')->count();
    }

    public function countKarte()
    {
        return $this->where('status', 'Karte')->count();
    }

    public function countKeinInteresse()
    {
        return $this->where('status', 'Kein Interesse')->count();
    }
}
