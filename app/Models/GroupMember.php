<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use app\Models\User;
use app\Models\Group;

class GroupMember extends Model
{
    protected $fillable = [
        'user_id',
        'group_id'
    ];

    // Group members can have multipe users
    public function user() {
        return $this->belongsTo(User::class);
    }

    // Group members can belong to one group
    public function group() {
        return $this->belongsTo(Group::class);
    }
}
