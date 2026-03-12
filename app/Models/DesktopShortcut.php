<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DesktopShortcut extends Model
{
    protected $fillable = [
        'user_id',
        'shortcut_id',
        'custom_name',
        'original_name',
        'position_x',
        'position_y'
    ];

    /**
     * Связь с пользователем
     */
    public function user()
    {
        return $this->belongsTo(AdminUser::class, 'user_id');
    }
}
