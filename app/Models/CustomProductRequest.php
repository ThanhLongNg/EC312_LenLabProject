<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CustomProductRequest extends Model
{
    protected $fillable = [
        'session_id',
        'user_id',
        'product_type',
        'size',
        'description',
        'contact_info',
        'reference_images',
        'status',
        'admin_notes',
        'admin_response',
        'admin_responded_at',
        'final_price',
        'estimated_completion_days',
        'payment_info',
        'payment_bill_image',
        'payment_submitted_at',
        'payment_confirmed_at',
        'shipping_address',
        'order_code',
        'cancelled_reason'
    ];

    protected $casts = [
        'reference_images' => 'array',
        'shipping_address' => 'array',
        'contact_info' => 'array',
        'payment_info' => 'array',
        'admin_responded_at' => 'datetime',
        'payment_submitted_at' => 'datetime',
        'payment_confirmed_at' => 'datetime'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getStatusTextAttribute(): string
    {
        return match($this->status) {
            'pending_info' => 'Đang thu thập thông tin',
            'pending_images' => 'Chờ upload ảnh',
            'pending_admin_response' => 'Chờ admin phản hồi',
            'in_discussion' => 'Đang trao đổi với admin',
            'awaiting_payment' => 'Chờ thanh toán',
            'payment_submitted' => 'Đã gửi bill - Chờ xác nhận',
            'paid' => 'Đã thanh toán - Đang sản xuất',
            'completed' => 'Hoàn thành',
            'cancelled' => 'Đã hủy',
            default => 'Không xác định'
        ];
    }

    // State machine methods
    public function canStartDiscussion(): bool
    {
        return $this->status === 'pending_admin_response';
    }

    public function canFinalize(): bool
    {
        return $this->status === 'in_discussion';
    }

    public function canCancel(): bool
    {
        return in_array($this->status, ['pending_admin_response', 'in_discussion']);
    }

    public function canPay(): bool
    {
        return $this->status === 'awaiting_payment' && $this->final_price > 0;
    }

    public function canConfirmPayment(): bool
    {
        return $this->status === 'payment_submitted';
    }

    // State transitions
    public function startDiscussion(): void
    {
        if (!$this->canStartDiscussion()) {
            throw new \Exception('Cannot start discussion from current status: ' . $this->status);
        }
        $this->update(['status' => 'in_discussion']);
    }

    public function finalizeRequest(float $price, int $completionDays): void
    {
        if (!$this->canFinalize()) {
            throw new \Exception('Cannot finalize request from current status: ' . $this->status);
        }
        
        $this->update([
            'status' => 'awaiting_payment',
            'final_price' => $price,
            'estimated_completion_days' => $completionDays
        ]);
    }

    public function cancelRequest(string $reason = null): void
    {
        if (!$this->canCancel()) {
            throw new \Exception('Cannot cancel request from current status: ' . $this->status);
        }
        
        $this->update([
            'status' => 'cancelled',
            'cancelled_reason' => $reason
        ]);
    }

    public function submitPayment(array $paymentInfo, string $billImagePath): void
    {
        if (!$this->canPay()) {
            throw new \Exception('Cannot submit payment from current status: ' . $this->status);
        }
        
        $this->update([
            'status' => 'payment_submitted',
            'payment_info' => $paymentInfo,
            'payment_bill_image' => $billImagePath,
            'payment_submitted_at' => now()
        ]);
    }

    public function confirmPayment(): void
    {
        if (!$this->canConfirmPayment()) {
            throw new \Exception('Cannot confirm payment from current status: ' . $this->status);
        }
        
        $this->update([
            'status' => 'paid',
            'payment_confirmed_at' => now()
        ]);
    }

    public function markCompleted(): void
    {
        if ($this->status !== 'paid') {
            throw new \Exception('Cannot complete from current status: ' . $this->status);
        }
        
        $this->update(['status' => 'completed']);
    }
}