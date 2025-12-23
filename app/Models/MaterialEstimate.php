<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MaterialEstimate extends Model
{
    protected $fillable = [
        'session_id',
        'user_id',
        'product_type',
        'size',
        'yarn_type',
        'estimated_materials',
        'total_estimated_cost',
        'added_to_cart'
    ];

    protected $casts = [
        'estimated_materials' => 'array',
        'added_to_cart' => 'boolean'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getMaterialsListAttribute(): string
    {
        if (!$this->estimated_materials) {
            return '';
        }

        $materials = [];
        foreach ($this->estimated_materials as $material) {
            $materials[] = $material['name'] . ': ' . $material['quantity'] . ' ' . $material['unit'];
        }

        return implode(', ', $materials);
    }
}