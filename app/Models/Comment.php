<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'product_id', 
        'order_id',
        'rating',
        'comment',
        'is_verified',
        'is_hidden'
    ];

    protected $casts = [
        'is_verified' => 'boolean',
        'is_hidden' => 'boolean',
        'rating' => 'integer'
    ];

    public $timestamps = false; // Only has created_at

    /**
     * Get the user who made the comment
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the product being commented on
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Get the order this comment belongs to
     */
    public function order()
    {
        return $this->belongsTo(Order::class, 'order_id', 'order_id');
    }

    /**
     * Get comment images
     */
    public function images()
    {
        return $this->hasMany(CommentImage::class);
    }

    /**
     * Get admin replies
     */
    public function replies()
    {
        return $this->hasMany(CommentReply::class);
    }

    /**
     * Scope for verified comments only
     */
    public function scopeVerified($query)
    {
        return $query->where('is_verified', true);
    }

    /**
     * Scope for visible comments only
     */
    public function scopeVisible($query)
    {
        return $query->where('is_hidden', false);
    }

    /**
     * Check if user can comment on this product for this order
     * Business rule: Only users who bought and received the product can comment
     */
    public static function canUserComment($userId, $productId, $orderId)
    {
        // Check if order exists, belongs to user, is delivered, and contains the product
        $order = Order::where('order_id', $orderId)
                     ->where('user_id', $userId)
                     ->where('status', 'delivered')
                     ->first();

        if (!$order) {
            return false;
        }

        // Check if the order contains this product
        $orderItem = OrderItem::where('order_id', $orderId)
                             ->where('product_id', $productId)
                             ->first();

        if (!$orderItem) {
            return false;
        }

        // Check if user already commented on this product for this order
        $existingComment = self::where('user_id', $userId)
                              ->where('product_id', $productId)
                              ->where('order_id', $orderId)
                              ->first();

        return !$existingComment;
    }

    /**
     * Create a new comment with business rule validation
     */
    public static function createComment($data)
    {
        // Validate business rules
        if (!self::canUserComment($data['user_id'], $data['product_id'], $data['order_id'])) {
            throw new \Exception('Bạn không thể đánh giá sản phẩm này. Chỉ khách hàng đã mua và nhận hàng mới có thể đánh giá.');
        }

        return self::create($data);
    }
}