<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Internship;

class AssesmentType extends Model
{
    protected $fillable = [
        'name',
    ];

    // An assessment type can have one internship
    public function internships() {
        return $this->hasMany(Internship::class, 'assessment_id');
    }
}
