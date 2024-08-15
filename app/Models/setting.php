<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class setting extends Model
{
    use HasFactory;
    protected $fillable = [
    'owner_id',
    'work_from',
    'work_to',
    'break_time',
    'time_zone',
    'logic',
    'max_app',
    'monday',
    'tuesday',
    'wednesday',
    'thursday',
    'friday',
    'saturday',
    'sunday',
    'max_duration_swicth',
    'max_duration_time',
    'min_time_switch',
    'min_time',
    'app_fixed_duration_switch',
    'app_fixed_duration',
    'allow_dm'
];

}