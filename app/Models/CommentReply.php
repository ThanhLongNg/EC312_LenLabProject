<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CommentReply extends Model
{
    use HasFactory;

    protected $fillable = [
        'comment_id',
        'admin_id',
        'reply'
    ];

    protected $casts = [
        'created_at' => 'datetime'
    ];

    public $timestamps = false; // Only has created_at

    /**
     * Get the comment this reply belongs to
     */
    public function comment()
    {
        return $this->belongsTo(Comment::class);
    }

    /**
     * Get the admin who made this reply
     */
    public function admin()
    {
        return $this->belongsTo(Admin::class);
    }
}