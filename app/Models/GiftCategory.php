<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class GiftCategory extends Model
{
    protected $fillable = [
        'name',
    ];

    /**
     * @return HasMany<GiftItem, $this>
     */
    public function items(): HasMany
    {
        return $this->hasMany(GiftItem::class);
    }
}
