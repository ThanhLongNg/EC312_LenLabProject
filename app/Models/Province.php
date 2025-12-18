<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Province extends Model
{
    protected $fillable = [
        'name',
        'slug'
    ];

    public function wards()
    {
        return $this->hasMany(Ward::class);
    }

    // Auto generate slug when creating
    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($province) {
            if (empty($province->slug)) {
                $province->slug = Str::slug($province->name);
            }
        });
    }
}