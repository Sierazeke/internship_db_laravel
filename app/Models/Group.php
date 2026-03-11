<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use app\Models\GroupMember;
use app\Models\Internship;

class Group extends Model
{
    protected $fillable = [
        'id',
        'name',
        'start_at',
        'end_at'
    ];

    // A group can have many users
    public function groupmembers() {
        return $this->hasMany(GroupMember::class);
    }

    // A group can have many internships
    public function internships() {
        return $this->hasMany(Internship::class);
    }
}
