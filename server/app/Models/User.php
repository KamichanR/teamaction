<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $guarded = [
        'id',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'uid',
        'pivot',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function organizations() {
        return $this->hasMany(Organization::class);
    }

    public function area()
    {
        return $this->belongsTo(Area::class);
    }

    public function workstyles()
    {
        return $this->belongsToMany(Workstyle::class);
    }

    public function skills()
    {
        return $this->belongsToMany(Skill::class);
    }

    public function careers()
    {
        return $this->hasMany(Career::class);
    }

    public function certifications()
    {
        return $this->hasMany(Certification::class);
    }
}
