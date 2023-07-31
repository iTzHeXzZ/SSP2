<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Street extends Model
{
    use HasFactory;
    public function assignedUser()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
