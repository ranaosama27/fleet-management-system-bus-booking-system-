<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class User_booking extends Model
{
    
    use HasFactory;
    protected $fillable = ['user_id', 'trip_main_id','trip_mid_id'];
}
