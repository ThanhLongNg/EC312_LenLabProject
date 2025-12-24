<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ChatSupportLog extends Model
{
    protected $fillable = [
        'custom_request_id',
        'sender_type',
        'sender_id',
        'message',
        'attachments',
        'is_read'
    ];

    protected $casts = [
        'attachments' => 'array',
        'is_read' => 'boolean'
    ];

    public function customProductRequest(): BelongsTo
    {
        return $this->belongsTo(CustomProductRequest::class);
    }

    public function sender(): BelongsTo
    {
        if ($this->sender_type === 'admin') {
            return $this->belongsTo(Admin::class, 'sender_id');
        }
        
        return $this->belongsTo(User::class, 'sender_id');
    }

    /**
     * Lấy tên người gửi
     */
    public function getSenderNameAttribute(): string
    {
        if ($this->sender_type === 'admin') {
            $admin = Admin::find($this->sender_id);
            return $admin ? $admin->name : 'Admin';
        }
        
        if ($this->sender_type === 'customer') {
            $user = User::find($this->sender_id);
            if ($user) {
                return $user->name;
            }
            
            // Nếu không có user_id, lấy từ contact_info của request
            $request = $this->customProductRequest;
            if ($request && $request->contact_info && isset($request->contact_info['name'])) {
                return $request->contact_info['name'];
            }
        }
        
        return 'Khách hàng';
    }

    /**
     * Đánh dấu đã đọc
     */
    public function markAsRead(): void
    {
        $this->update(['is_read' => true]);
    }

    /**
     * Scope: Tin nhắn chưa đọc
     */
    public function scopeUnread($query)
    {
        return $query->where('is_read', false);
    }

    /**
     * Scope: Tin nhắn từ khách hàng
     */
    public function scopeFromCustomer($query)
    {
        return $query->where('sender_type', 'customer');
    }

    /**
     * Scope: Tin nhắn từ admin
     */
    public function scopeFromAdmin($query)
    {
        return $query->where('sender_type', 'admin');
    }
}