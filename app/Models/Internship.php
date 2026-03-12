<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\AssesmentType;
use App\Models\Group;
use App\Models\Company;
use App\Models\Application;

class Internship extends Model
{
    protected $fillable = [
        'company_id',
        'group_id',
        'title',
        'description',
        'goals',
        'start_at',
        'end_at',
        'assessment_id'
    ];

    // An internship has one assesment type
    public function assessment() {
        return $this->belongsTo(AssesmentType::class, 'assessment_id');
    }

    // An intership has many groups
    public function group() {
        return $this->belongsTo(Group::class);
    }

    // Multiple internships belongs to one company
    public function company() {
        return $this->belongsTo(Company::class);
    }

    // Internship has many applications
    public function applications() {
        return $this->hasMany(Application::class);
    }
}
