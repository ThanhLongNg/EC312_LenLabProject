<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CommentImage extends Model
{
    use HasFactory;

    protected $fillable = [
        'comment_id',
        'image_path'
    ];

    public $timestamps = false; // Only has created_at

    /**
     * Get the comment this image belongs to
     */
    public function comment()
    {
        return $this->belongsTo(Comment::class);
    }

    /**
     * Get the full image URL
     */
    public function getImageUrlAttribute()
    {
        return asset('comment-images/' . $this->image_path);
    }
}