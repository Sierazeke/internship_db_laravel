<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Models\Role;
use App\Models\GroupMember;
use App\Models\Evaluation;
use App\Models\Application;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
    'name',
    'email',
    'password',
    'role_id'
    ];

    // A user belongs to one role
    public function role() {
        return $this->belongsTo(Role::class);
    }

    // A user can have many group members
    public function groupmembers() {
        return $this->hasMany(GroupMember::class);
    }

     // A user can have many evaluations
     public function evaluations() {
        return $this->hasMany(Evaluation::class);
    }

     // A user can have many applications
     public function applications() {
        return $this->hasMany(Application::class);
    }

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
}
