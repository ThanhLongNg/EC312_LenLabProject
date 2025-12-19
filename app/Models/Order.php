<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    public $timestamps = false; // Tắt timestamps vì bảng không có created_at, updated_at
    protected $table = 'orders';
    protected $primaryKey = 'order_id';
    public $incrementing = false;
    protected $keyType = 'string';
    protected $fillable = [
        'order_id',
        'order_code',
        'user_id',
        'full_name',
        'phone',
        'email',
        'province',
        'district',
        'specific_address',
        'shipping_method',   // 'store' / 'delivery'
        'shipping_fee',
        'discount_amount',
        'total_amount',
        'status',            // 'pending', 'confirmed', ...
        'payment_method',    // 'cod', 'bank_transfer', ...
        'order_note',        // Sửa từ 'note' thành 'order_note'
        'created_at',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function items()
    {
        return $this->hasMany(OrderItem::class, 'order_id', 'order_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
