<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Application;
use App\Models\User;

class Evaluation extends Model
{
    protected $fillable = [
        'id',
        'score',
        'comment',
        'application_id',
    ];

    // An evaluation belongs to one application
    public function application() {
        return $this->belongsTo(Application::class);
    }

    // An evaluation can have a user
    public function user() {
        return $this->belongsTo(User::class);
    }

}
