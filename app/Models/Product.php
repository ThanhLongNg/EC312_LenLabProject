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
        'images',
        'description',
        'status',
        'category_id',
        'color',
        'size',
        'new',
        'is_active'
    ];

    protected $casts = [
        'images' => 'array',
        'is_active' => 'boolean'
    ];

    // Scope để lấy sản phẩm active
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    // Scope để lấy sản phẩm inactive
    public function scopeInactive($query)
    {
        return $query->where('is_active', false);
    }

    public function variants()
    {
        return $this->hasMany(ProductVariant::class);
    }
    
    // Kiểm tra xem sản phẩm có nhiều biến thể không
    public function hasMultipleVariants()
    {
        return $this->variants()->count() > 1;
    }
    
    // Lấy tất cả tên biến thể có sẵn
    public function getAvailableVariants()
    {
        return $this->variants()->whereNotNull('variant_name')->pluck('variant_name');
    }
    
    // Kiểm tra xem có nhiều biến thể không
    public function hasVariants()
    {
        return $this->variants()->count() > 0;
    }

    // Lấy tất cả hình ảnh của sản phẩm (bao gồm hình chính và hình phụ)
    public function getAllImages()
    {
        $images = [];
        
        // Thêm hình ảnh chính
        if ($this->image) {
            $images[] = $this->image;
        }
        
        // Thêm hình ảnh phụ nếu có (giả sử có cột images chứa JSON array)
        if (isset($this->images) && is_array($this->images)) {
            $images = array_merge($images, $this->images);
        }
        
        return array_unique($images);
    }

    // Kiểm tra xem có nhiều hình ảnh không
    public function hasMultipleImages()
    {
        return count($this->getAllImages()) > 1;
    }
}
