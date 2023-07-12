<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    public $timestamps = false;
    protected $fillable = ['id','ort','postleitzahl','strasse','hausnummer','wohneinheiten','bestand','notiz','bearbeitungsdatum'];
}
