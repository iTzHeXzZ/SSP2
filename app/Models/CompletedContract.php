<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CompletedContract extends Model
{
    protected $fillable = ['user_id', 'ort', 'adresse', 'status', 'notiz', 'kundenname', 'gfpaket', 'fritzbox', 'firstflat'];

    public function user() {
        return $this->belongsTo(User::class);
    }
}
