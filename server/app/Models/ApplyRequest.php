<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ApplyRequest extends Model
{
    use HasFactory;

    protected $guarded = [
        'applied_at',
    ];

    public $timestamps = false;
}
