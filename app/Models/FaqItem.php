<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class FaqItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'category',
        'keywords',
        'question',
        'answer',
        'priority',
        'is_active',
        'usage_count'
    ];

    protected $casts = [
        'keywords' => 'array',
        'is_active' => 'boolean',
        'priority' => 'integer',
        'usage_count' => 'integer'
    ];

    /**
     * Scope để lấy FAQ đang hoạt động
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope để sắp xếp theo độ ưu tiên
     */
    public function scopeByPriority($query)
    {
        return $query->orderBy('priority', 'desc')->orderBy('usage_count', 'desc');
    }

    /**
     * Tìm kiếm FAQ theo từ khóa
     */
    public static function searchByKeywords(string $message): ?self
    {
        $message = strtolower(trim($message));
        
        return self::active()
            ->byPriority()
            ->get()
            ->first(function ($faq) use ($message) {
                // Tìm kiếm trong keywords
                $keywords = $faq->keywords ?? [];
                foreach ($keywords as $keyword) {
                    if (str_contains($message, strtolower($keyword))) {
                        return true;
                    }
                }
                
                // Tìm kiếm trong nội dung câu hỏi
                $question = strtolower($faq->question);
                $questionWords = explode(' ', $question);
                
                // Kiểm tra từng từ trong câu hỏi
                foreach ($questionWords as $word) {
                    $word = trim($word, '?.,!');
                    if (strlen($word) > 2 && str_contains($message, $word)) {
                        return true;
                    }
                }
                
                // Kiểm tra toàn bộ câu hỏi
                if (str_contains($message, $question) || str_contains($question, $message)) {
                    return true;
                }
                
                return false;
            });
    }

    /**
     * Tăng số lần sử dụng
     */
    public function incrementUsage(): void
    {
        $this->increment('usage_count');
    }

    /**
     * Lấy danh sách categories
     */
    public static function getCategories(): array
    {
        return [
            'giao_hang' => 'Giao hàng & Vận chuyển',
            'doi_tra' => 'Đổi trả & Hoàn tiền',
            'san_pham' => 'Sản phẩm & Chất liệu',
            'thanh_toan' => 'Thanh toán & Bảo mật',
            'ho_tro' => 'Hỗ trợ & Liên hệ',
            'general' => 'Tổng quát'
        ];
    }

    /**
     * Lấy tên category
     */
    public function getCategoryNameAttribute(): string
    {
        $categories = self::getCategories();
        return $categories[$this->category] ?? 'Không xác định';
    }
}
