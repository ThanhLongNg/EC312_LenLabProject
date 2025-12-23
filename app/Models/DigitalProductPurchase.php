<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class DigitalProductPurchase extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'digital_product_id',
        'customer_email',
        'customer_name',
        'order_code',
        'purchase_price',
        'amount_paid',
        'purchased_at',
        'expires_at',
        'download_count',
        'downloads_count',
        'email_sent',
        'download_history',
        'download_links',
        'transfer_image',
        'status'
    ];

    protected $casts = [
        'purchased_at' => 'datetime',
        'expires_at' => 'datetime',
        'email_sent' => 'boolean',
        'download_history' => 'array',
        'download_links' => 'array',
        'amount_paid' => 'decimal:2',
        'purchase_price' => 'decimal:2'
    ];

    public function digitalProduct()
    {
        return $this->belongsTo(DigitalProduct::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function comments()
    {
        return $this->hasMany(Comment::class, 'digital_purchase_id');
    }

    public function isExpired()
    {
        return $this->expires_at && Carbon::now()->isAfter($this->expires_at);
    }

    public function canDownload()
    {
        if ($this->isExpired()) {
            return false;
        }

        $downloadCount = $this->downloads_count ?? $this->download_count ?? 0;
        return $downloadCount < $this->digitalProduct->download_limit;
    }

    public function recordDownload()
    {
        $history = $this->download_history ?? [];
        $history[] = [
            'downloaded_at' => now()->toISOString(),
            'ip_address' => request()->ip()
        ];

        $currentCount = $this->downloads_count ?? $this->download_count ?? 0;
        
        $this->update([
            'downloads_count' => $currentCount + 1,
            'download_count' => $currentCount + 1,
            'download_history' => $history
        ]);
    }

    /**
     * Get the status attribute, calculating it if not set
     */
    public function getStatusAttribute($value)
    {
        // If status is explicitly set, return it
        if ($value) {
            return $value;
        }

        // Calculate status based on conditions
        if ($this->isExpired()) {
            return 'expired';
        }

        // If we have a purchase date, it's active
        if ($this->purchased_at) {
            return 'active';
        }

        return 'pending';
    }

    /**
     * Get downloads count (support both field names)
     */
    public function getDownloadsCountAttribute($value)
    {
        return $value ?? $this->download_count ?? 0;
    }

    /**
     * Get amount paid (support both field names)
     */
    public function getAmountPaidAttribute($value)
    {
        return $value ?? $this->purchase_price ?? 0;
    }
}