<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use app\Models\User;

class Role extends Model
{
    protected $fillable = [
        'id',
        'name',
    ];

    // A role can have many users
    public function users() {
        return $this->hasMany(User::class);
    }
}
