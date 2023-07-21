<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    public $timestamps = true;
    protected $fillable = ['id','ort','postleitzahl','strasse','hausnummer','wohneinheiten','bestand','notiz', 'status','created_at','updatet_at'];
}
