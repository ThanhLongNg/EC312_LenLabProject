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
    
    public function comments()
    {
        return $this->hasMany(Comment::class);
    }
    
    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
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
    
    /**
     * Lấy đường dẫn ảnh thông minh - tự động tìm ở cả 2 thư mục
     * Ưu tiên storage/products trước vì đây là thư mục chính chứa ảnh
     */
    public function getImagePath()
    {
        if (!$this->image || $this->image === 'default.jpg') {
            return null;
        }
        
        // Kiểm tra thư mục storage/products trước (thư mục chính)
        $storagePath = public_path('storage/products/' . $this->image);
        if (file_exists($storagePath)) {
            return '/storage/products/' . $this->image;
        }
        
        // Kiểm tra thư mục product-img (thư mục backup)
        $productImgPath = public_path('product-img/' . $this->image);
        if (file_exists($productImgPath)) {
            return '/product-img/' . $this->image;
        }
        
        // Nếu không tìm thấy ở cả 2 thư mục, trả về đường dẫn mặc định
        return '/storage/products/' . $this->image;
    }
    
    /**
     * Lấy URL ảnh với cache busting
     */
    public function getImageUrl($timestamp = null)
    {
        $path = $this->getImagePath();
        if (!$path) {
            return null;
        }
        
        $timestamp = $timestamp ?: time();
        return $path . '?v=' . $timestamp;
    }
}
