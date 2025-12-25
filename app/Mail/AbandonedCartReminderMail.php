<?php

namespace App\Mail;

use App\Models\User;
use App\Models\Voucher;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Collection;

class AbandonedCartReminderMail extends Mailable
{
    use Queueable, SerializesModels;

    public User $user;
    public Collection $cartItems;
    public float $total;
    public ?Voucher $voucher;

    /**
     * Create a new message instance.
     */
    public function __construct(User $user, Collection $cartItems, float $total, ?Voucher $voucher = null)
    {
        $this->user = $user;
        $this->cartItems = $cartItems;
        $this->total = $total;
        $this->voucher = $voucher;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $subject = $this->voucher 
            ? '🎁 Mã giảm giá đặc biệt cho bạn - Đừng bỏ lỡ giỏ hàng!'
            : '🛒 Bạn đã quên giỏ hàng của mình - Hoàn tất đơn hàng ngay!';

        return new Envelope(
            subject: $subject,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.abandoned-cart-reminder',
            with: [
                'user' => $this->user,
                'cartItems' => $this->cartItems,
                'total' => $this->total,
                'voucher' => $this->voucher,
            ]
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}