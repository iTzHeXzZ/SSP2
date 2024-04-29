<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubProject extends Model
{
    use HasFactory;
    protected $fillable = ['project_id', 'wohnung_nr', 'status', 'notiz'];
}
