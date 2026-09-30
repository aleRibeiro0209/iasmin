<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GiftItem extends Model
{
    protected $fillable = [
        'name',
        'gift_category_id',
    ];

    /**
     * @return BelongsTo<GiftCategory, $this>
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(GiftCategory::class, 'gift_category_id');
    }
}
