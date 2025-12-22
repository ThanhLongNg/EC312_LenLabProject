<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DigitalProduct extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'price',
        'type',
        'files',
        'links',
        'instructions',
        'auto_send_email',
        'email_template',
        'thumbnail',
        'is_active',
        'download_limit',
        'access_days'
    ];

    protected $casts = [
        'files' => 'array',
        'links' => 'array',
        'auto_send_email' => 'boolean',
        'is_active' => 'boolean',
        'price' => 'decimal:2'
    ];

    public function purchases()
    {
        return $this->hasMany(DigitalProductPurchase::class);
    }

    public function getFormattedPriceAttribute()
    {
        return number_format($this->price) . '₫';
    }

    public function getThumbnailUrlAttribute()
    {
        if ($this->thumbnail && \Storage::disk('public')->exists($this->thumbnail)) {
            return asset('storage/' . $this->thumbnail);
        }
        return null; // Return null instead of non-existent default image
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}