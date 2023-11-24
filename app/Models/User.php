<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as AuthUser;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class User extends AuthUser
{
    use HasFactory;

    protected $fillable = [
        'name',
        'username',
        'password'
    ];

    public function contact()
    {
        return $this->belongsTo(Contact::class);
    }
}
