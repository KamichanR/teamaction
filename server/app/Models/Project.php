<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    use HasFactory;

    protected $guarded = [
        'id'
    ];

    public function area()
    {
        return $this->belongsTo(Area::class);
    }

    public function projectCategory()
    {
        return $this->belongsTo(ProjectCategory::class);
    }

    public function skills()
    {
        return $this->belongsToMany(Skill::class);
    }

    public function projectRoles()
    {
        return $this->hasMany(ProjectRole::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
