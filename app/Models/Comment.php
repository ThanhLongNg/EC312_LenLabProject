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
        'digital_product_id',
        'digital_purchase_id',
        'rating',
        'comment',
        'is_verified',
        'is_hidden'
    ];

    protected $casts = [
        'is_verified' => 'boolean',
        'is_hidden' => 'boolean',
        'rating' => 'integer',
        'created_at' => 'datetime'
    ];

    public $timestamps = false; // Only has created_at, no updated_at

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
     * Get the digital product being commented on
     */
    public function digitalProduct()
    {
        return $this->belongsTo(DigitalProduct::class);
    }

    /**
     * Get the order this comment belongs to
     */
    public function order()
    {
        return $this->belongsTo(Order::class, 'order_id', 'order_id');
    }
    
    /**
     * Get the digital purchase this comment belongs to
     */
    public function digitalPurchase()
    {
        return $this->belongsTo(DigitalProductPurchase::class);
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
     * Check if user can comment on regular product for this order
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
     * Check if user can comment on digital product for this purchase
     * Business rule: Only users who bought the digital product can comment
     */
    public static function canUserCommentDigitalProduct($userId, $digitalProductId, $digitalPurchaseId = null)
    {
        // If specific purchase ID is provided, check that purchase
        if ($digitalPurchaseId) {
            $purchase = DigitalProductPurchase::where('id', $digitalPurchaseId)
                                             ->where('user_id', $userId)
                                             ->where('digital_product_id', $digitalProductId)
                                             ->first();
            
            if (!$purchase) {
                return false;
            }
            
            // Check if user already commented on this digital product for this purchase
            $existingComment = self::where('user_id', $userId)
                                  ->where('digital_product_id', $digitalProductId)
                                  ->where('digital_purchase_id', $digitalPurchaseId)
                                  ->first();
            
            return !$existingComment;
        }

        // Check if user has any purchase of this digital product
        $hasPurchase = DigitalProductPurchase::where('user_id', $userId)
                                           ->where('digital_product_id', $digitalProductId)
                                           ->exists();

        if (!$hasPurchase) {
            return false;
        }

        // Check if user already commented on this digital product (any purchase)
        $existingComment = self::where('user_id', $userId)
                              ->where('digital_product_id', $digitalProductId)
                              ->first();

        return !$existingComment;
    }

    /**
     * Get user's eligible orders for reviewing a product
     */
    public static function getUserEligibleOrders($userId, $productId)
    {
        return Order::where('user_id', $userId)
                   ->where('status', 'delivered')
                   ->whereHas('items', function($query) use ($productId) {
                       $query->where('product_id', $productId);
                   })
                   ->whereDoesntHave('comments', function($query) use ($productId) {
                       $query->where('product_id', $productId);
                   })
                   ->get();
    }

    /**
     * Get user's eligible digital purchases for reviewing a digital product
     */
    public static function getUserEligibleDigitalPurchases($userId, $digitalProductId)
    {
        return DigitalProductPurchase::where('user_id', $userId)
                                   ->where('digital_product_id', $digitalProductId)
                                   ->whereDoesntHave('comments')
                                   ->get();
    }

    /**
     * Create a new comment with business rule validation
     */
    public static function createComment($data)
    {
        // Validate business rules for regular products
        if (isset($data['product_id']) && isset($data['order_id'])) {
            if (!self::canUserComment($data['user_id'], $data['product_id'], $data['order_id'])) {
                throw new \Exception('Bạn không thể đánh giá sản phẩm này. Chỉ khách hàng đã mua và nhận hàng mới có thể đánh giá.');
            }
        }

        // Validate business rules for digital products
        if (isset($data['digital_product_id'])) {
            $digitalPurchaseId = $data['digital_purchase_id'] ?? null;
            if (!self::canUserCommentDigitalProduct($data['user_id'], $data['digital_product_id'], $digitalPurchaseId)) {
                throw new \Exception('Bạn không thể đánh giá sản phẩm số này. Chỉ khách hàng đã mua sản phẩm mới có thể đánh giá.');
            }
        }

        return self::create($data);
    }
}