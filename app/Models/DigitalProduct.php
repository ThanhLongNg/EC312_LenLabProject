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

    public function comments()
    {
        return $this->hasMany(Comment::class, 'digital_product_id');
    }

    public function verifiedComments()
    {
        return $this->hasMany(Comment::class, 'digital_product_id')->verified()->visible();
    }

    public function getFormattedPriceAttribute()
    {
        return number_format($this->price) . '₫';
    }

    public function getThumbnailUrlAttribute()
    {
        if ($this->thumbnail) {
            // Check if it's a storage path
            if (\Storage::disk('public')->exists($this->thumbnail)) {
                return asset('storage/' . $this->thumbnail);
            }
            // Check if it's a public asset path
            if (file_exists(public_path($this->thumbnail))) {
                return asset($this->thumbnail);
            }
        }
        return null;
    }

    public function getAverageRatingAttribute()
    {
        return $this->verifiedComments()->avg('rating') ?: 0;
    }

    public function getReviewCountAttribute()
    {
        return $this->verifiedComments()->count();
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}