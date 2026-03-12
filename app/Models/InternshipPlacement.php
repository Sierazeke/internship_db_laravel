<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Application;
use App\Models\Company;

class InternshipPlacement extends Model
{
    protected $fillable = [
        'id',
        'application_id',
        'company_id',
        'start_at',
        'end_at'
    ];

    // Internship placement belongs to one application
    public function application() {
        return $this->belongsTo(Application::class);
    }

    // Internship placement belongs to one company
    public function company() {
        return $this->belongsTo(Company::class);
    }
}
