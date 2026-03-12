<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\Internship;
use App\Models\Evaluation;
use App\Models\InternshipPlacement;

class Application extends Model
{
    protected $fillable = [
        'motivation_letter',    
        'is_approved',
        'approved_at',
    ];

    // Who submitted the application
    public function user() {
        return $this->belongsTo(User::class);
    }

    // Which internship its for
    public function internship() {
        return $this->belongsTo(Internship::class);
    }

    // Application can have one evaluation
    public function evaluation() {
        return $this->hasOne(Evaluation::class);
    }

    // Application can result in one placement
    public function placement() {
        return $this->hasOne(InternshipPlacement::class);
    }
}
