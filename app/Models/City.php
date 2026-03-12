<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class City extends Model
{
    protected $table = 'cities';

    protected $fillable = [
        'name',
        'slug',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    /**
     * Получить список активных городов
     */
    public static function getActiveCities()
    {
        return self::where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();
    }

    /**
     * Поиск городов по названию
     */
    public static function searchByName($query)
    {
        return self::where('is_active', true)
            ->where('name', 'LIKE', '%' . $query . '%')
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();
    }
}
