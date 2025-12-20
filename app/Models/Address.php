<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Address extends Model
{
    protected $fillable = [
        'user_id',
        'full_name',
        'phone',
        'province_id',
        'ward_id',
        'specific_address',
        'is_default',
        'detail', // Keep old field for backward compatibility
    ];

    protected $casts = [
        'is_default' => 'boolean',
    ];

    // Quan hệ user
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Quan hệ tỉnh
    public function province()
    {
        return $this->belongsTo(Province::class);
    }

    // Quan hệ phường/xã
    public function ward()
    {
        return $this->belongsTo(Ward::class);
    }

    // Địa chỉ đầy đủ để hiển thị
    public function getFullAddressAttribute()
    {
        $provinceName = $this->province?->name ?? '';
        $wardName = $this->ward?->name ?? '';

        return trim("{$this->specific_address}, {$wardName}, {$provinceName}", ', ');
    }
}
