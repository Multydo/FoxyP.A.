<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class personal_access_token extends Model
{
    use HasFactory;
    protected $fillable = [
        'token',
        'last_used_at',
    'created_at'
];
}