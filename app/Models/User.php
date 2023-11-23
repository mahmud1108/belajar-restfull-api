<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    use HasFactory;

    protected $guarded = 'id';

    public function contact()
    {
        return $this->belongsTo(Contact::class);
    }
}
