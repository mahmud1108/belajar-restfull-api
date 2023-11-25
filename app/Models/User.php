<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    use HasFactory;

    protected $fillable = [
        'username',
        'name',
        'password'
    ];

    public function contact()
    {
        return $this->hasMany(Contact::class);
    }
}
