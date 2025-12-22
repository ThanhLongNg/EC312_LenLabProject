<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Banner extends Model
{
    protected $fillable = [
        'position', 'title', 'link', 'image', 'is_active'
    ];
}
