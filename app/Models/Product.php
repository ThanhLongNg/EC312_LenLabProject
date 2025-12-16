<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    public $timestamps = false; // Tắt timestamps vì bảng không có created_at, updated_at
    
    protected $fillable = [
        'name',
        'price',
        'quantity',
        'image',
        'description',
        'status',
        'category_id',
        'color',
        'size',
        'new'
    ];

    public function variants()
    {
        return $this->hasMany(ProductVariant::class);
    }
}
