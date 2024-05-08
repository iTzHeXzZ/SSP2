<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ArchivedProject extends Model
{
    use HasFactory;
    public $timestamps = true;

    protected $fillable = ['id', 'ort', 'postleitzahl', 'strasse', 'hausnummer', 'wohneinheiten', 'bestand', 'notiz', 'status', 'created_at', 'updated_at'];

    public function subProjects()
    {
        return $this->hasMany(SubProject::class);
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
    
    public function countKeinPotenzial()
    {
        return $this->where('status', 'Kein Potenzial')->count();
    }
    
    public function countFremdVP()
    {
        return $this->where('status', 'Fremd VP')->count();
    }    
}
