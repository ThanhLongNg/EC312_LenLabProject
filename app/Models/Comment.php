<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class Comment extends Model
{
    use HasFactory;

    protected $table = 'comments';

    protected $fillable = [
        'user_id',
        'product_id',
        'order_id',
        'digital_product_id',
        'digital_purchase_id',
        'rating',
        'comment',
        'is_verified',
        'is_hidden',
        // nếu bảng bạn có created_at thì vẫn OK
        'created_at',
    ];

    protected $casts = [
        'is_verified' => 'boolean',
        'is_hidden'   => 'boolean',
        'rating'      => 'integer',
        'created_at'  => 'datetime',
    ];

    // Bảng comments chỉ có created_at, không có updated_at
    public $timestamps = false;

    /* =========================
     * RELATIONS
     * ========================= */

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function digitalProduct()
    {
        return $this->belongsTo(DigitalProduct::class);
    }

    public function order()
    {
        return $this->belongsTo(Order::class, 'order_id', 'order_id');
    }

    public function digitalPurchase()
    {
        return $this->belongsTo(DigitalProductPurchase::class, 'digital_purchase_id');
    }

    public function images()
    {
        return $this->hasMany(CommentImage::class);
    }

    public function replies()
    {
        return $this->hasMany(CommentReply::class);
    }

    /* =========================
     * SCOPES
     * ========================= */

    // Chờ duyệt: chưa verified
    public function scopePending($query)
    {
        return $query->where('is_verified', 0);
    }

    // Đã duyệt: verified = 1 và không bị ẩn
    public function scopeApproved($query)
    {
        return $query->where('is_verified', 1)
                     ->where('is_hidden', 0);
    }

    // Bị ẩn
    public function scopeHidden($query)
    {
        return $query->where('is_hidden', 1);
    }

    // Lọc theo số sao
    public function scopeWithRating($query, $rating)
    {
        return $query->where('rating', (int) $rating);
    }

    // Có hình ảnh (dựa vào relation images)
    public function scopeWithImages($query)
    {
        return $query->whereHas('images');
    }

    // Hiển thị ngoài user: không ẩn
    public function scopeVisible($query)
    {
        return $query->where('is_hidden', 0);
    }

    // Đã xác thực
    public function scopeVerified($query)
    {
        return $query->where('is_verified', 1);
    }

    /* =========================
     * COMPUTED / HELPERS
     * ========================= */

    // status ảo để dùng trong blade: $review->status
    public function getStatusAttribute(): string
    {
        if ((int) $this->is_hidden === 1) {
            return 'hidden';
        }
        if ((int) $this->is_verified === 1) {
            return 'approved';
        }
        return 'pending';
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isApproved(): bool
    {
        return $this->status === 'approved';
    }

    public function isHidden(): bool
    {
        return $this->status === 'hidden';
    }

    // formatted_created_at để blade dùng: $review->formatted_created_at
    public function getFormattedCreatedAtAttribute(): string
    {
        if (!$this->created_at) return '';
        return Carbon::parse($this->created_at)->format('d/m/Y H:i');
    }

    // image_urls để blade foreach: $review->image_urls
    public function getImageUrlsAttribute(): array
    {
        // Tùy bạn lưu path gì trong CommentImage, mình làm kiểu phổ biến:
        // - nếu lưu "storage/comments/xxx.jpg" hoặc "/storage/..." thì trả luôn
        // - nếu chỉ lưu filename, thì ghép vào storage/comments/
        $urls = [];

        foreach ($this->images ?? [] as $img) {
            $path = $img->image_path ?? $img->path ?? $img->image ?? null;
            if (!$path) continue;

            if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
                $urls[] = $path;
            } elseif (str_starts_with($path, '/')) {
                $urls[] = $path;
            } elseif (str_starts_with($path, 'storage/') || str_starts_with($path, 'uploads/')) {
                $urls[] = '/' . ltrim($path, '/');
            } else {
                // fallback: bạn chỉnh folder đúng nơi bạn lưu ảnh comment
                $urls[] = '/storage/comments/' . ltrim($path, '/');
            }
        }

        return $urls;
    }

    /* =========================
     * BUSINESS RULES (giữ lại)
     * ========================= */

    public static function canUserComment($userId, $productId, $orderId)
    {
        $order = Order::where('order_id', $orderId)
            ->where('user_id', $userId)
            ->where('status', 'delivered')
            ->first();

        if (!$order) {
            return false;
        }

        $orderItem = OrderItem::where('order_id', $orderId)
            ->where('product_id', $productId)
            ->first();

        if (!$orderItem) {
            return false;
        }

        $existingComment = self::where('user_id', $userId)
            ->where('product_id', $productId)
            ->where('order_id', $orderId)
            ->first();

        return !$existingComment;
    }

    public static function canUserCommentDigitalProduct($userId, $digitalProductId, $digitalPurchaseId = null)
    {
        if ($digitalPurchaseId) {
            $purchase = DigitalProductPurchase::where('id', $digitalPurchaseId)
                ->where('user_id', $userId)
                ->where('digital_product_id', $digitalProductId)
                ->first();

            if (!$purchase) return false;

            $existingComment = self::where('user_id', $userId)
                ->where('digital_product_id', $digitalProductId)
                ->where('digital_purchase_id', $digitalPurchaseId)
                ->first();

            return !$existingComment;
        }

        $hasPurchase = DigitalProductPurchase::where('user_id', $userId)
            ->where('digital_product_id', $digitalProductId)
            ->exists();

        if (!$hasPurchase) return false;

        $existingComment = self::where('user_id', $userId)
            ->where('digital_product_id', $digitalProductId)
            ->first();

        return !$existingComment;
    }

    public static function getUserEligibleOrders($userId, $productId)
    {
        return Order::where('user_id', $userId)
            ->where('status', 'delivered')
            ->whereHas('items', function ($query) use ($productId) {
                $query->where('product_id', $productId);
            })
            ->whereDoesntHave('comments', function ($query) use ($productId) {
                $query->where('product_id', $productId);
            })
            ->get();
    }

    public static function getUserEligibleDigitalPurchases($userId, $digitalProductId)
    {
        return DigitalProductPurchase::where('user_id', $userId)
            ->where('digital_product_id', $digitalProductId)
            ->whereDoesntHave('comments')
            ->get();
    }

    public static function createComment($data)
    {
        if (isset($data['product_id']) && isset($data['order_id'])) {
            if (!self::canUserComment($data['user_id'], $data['product_id'], $data['order_id'])) {
                throw new \Exception('Bạn không thể đánh giá sản phẩm này. Chỉ khách hàng đã mua và nhận hàng mới có thể đánh giá.');
            }
        }

        if (isset($data['digital_product_id'])) {
            $digitalPurchaseId = $data['digital_purchase_id'] ?? null;
            if (!self::canUserCommentDigitalProduct($data['user_id'], $data['digital_product_id'], $digitalPurchaseId)) {
                throw new \Exception('Bạn không thể đánh giá sản phẩm số này. Chỉ khách hàng đã mua sản phẩm mới có thể đánh giá.');
            }
        }

        return self::create($data);
    }
}
