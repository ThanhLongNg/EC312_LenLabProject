<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductVariant extends Model
{
    protected $table = 'product_variants';
    public $timestamps = false;
    
    protected $fillable = [
        'product_id',
        'variant_name',
        'image',
        'price'
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}