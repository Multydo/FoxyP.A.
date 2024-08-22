<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class followers_user extends Model
{
    use HasFactory;
    protected $fillable =[
        'userId',
        'followers_num',
        'following_num'
        
    ];

}