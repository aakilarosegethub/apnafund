<?php

namespace App\Models\Admins;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Admin extends Model
{
    use HasFactory;
    
    protected $table = 'admins';
    
    protected $fillable = [
        'email', 
        'password'
    ];
    
    protected $hidden = [
        'password'
    ];
}
