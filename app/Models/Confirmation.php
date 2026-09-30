<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Confirmation extends Model
{
    protected $fillable = [
        'name',
        'attending',
        'guests',
        'companions',
        'message',
    ];

    protected function casts(): array
    {
        return [
            'attending' => 'boolean',
            'guests' => 'integer',
            'companions' => 'array',
        ];
    }
}
