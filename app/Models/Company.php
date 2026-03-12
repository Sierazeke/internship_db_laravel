<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Internship;
use App\Models\InternshipPlacement;

class Company extends Model
{
    protected $fillable = [
        'id',
        'name',
        'address'
    ];

    // A company can have many internships
    public function internships() {
        return $this->hasMany(Internship::class);
    }

    // A company can have many internship placements
    public function placements() {
        return $this->hasMany(InternshipPlacement::class);
    }
}
