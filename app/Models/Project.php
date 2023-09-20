<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    public $timestamps = true;
    protected $fillable = ['id','ort','postleitzahl','strasse','hausnummer','wohneinheiten','bestand','notiz', 'status','created_at','updatet_at'];
    
    public function users()
    {
        return $this->belongsToMany(User::class)
        ->withPivot('hausnummer');
    }

    public function strasse()
    {
        // Annahme: Die Straßen sind in der Tabelle "projects" gespeichert,
        // und es gibt eine Spalte namens "strasse" in der Tabelle "projects".
        return $this->hasMany(Project::class, 'strasse');
    }
}
