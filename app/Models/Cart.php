<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cart extends Model
{
    use HasFactory;

    protected $table = 'cart'; // Specify table name

    protected $fillable = [
        'user_id',
        'product_id',
        'variant_id',
        'quantity',
        'price_at_time',
        'variant_info',
        'session_id'
    ];

    protected $casts = [
        'variant_info' => 'array',
        'price_at_time' => 'decimal:2'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
    public function variant()
    {
        return $this->belongsTo(ProductVariant::class, 'variant_id'); 
    }


}