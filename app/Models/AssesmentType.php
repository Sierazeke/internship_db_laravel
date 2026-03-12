<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Internship;

class AssesmentType extends Model
{
    protected $fillable = [
        'id',
        'name',
    ];

    // An assessment type can have one internship
    public function assessments() {
        return $this->hasOne(Internship::class);
    }
}
