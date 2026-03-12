<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WindowState extends Model
{
    protected $fillable = [
        'user_id',
        'window_id',
        'x',
        'y',
        'width',
        'height',
        'maximized',
    ];

    protected $casts = [
        'maximized' => 'boolean',
    ];

    /**
     * Связь с пользователем
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
