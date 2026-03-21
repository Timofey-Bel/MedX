<?php

namespace App\Helpers;

class ImageHelper
{
    /**
     * Получить URL изображения из категории
     *
     * @param string $category Категория (branding, medical, ui, backgrounds, social, main, icons, pages)
     * @param string $filename Имя файла
     * @param string|null $subcategory Подкатегория (для icons: menu/settings/tests, для pages: about/register/footer)
     * @return string
     */
    public static function get(string $category, string $filename, ?string $subcategory = null): string
    {
        $path = "images/{$category}";
        
        if ($subcategory) {
            $path .= "/{$subcategory}";
        }
        
        return asset("{$path}/{$filename}");
    }

    // Shortcuts для частых категорий
    public static function branding(string $filename): string
    {
        return self::get('branding', $filename);
    }

    public static function medical(string $filename): string
    {
        return self::get('medical', $filename);
    }

    public static function ui(string $filename): string
    {
        return self::get('ui', $filename);
    }

    public static function background(string $filename): string
    {
        return self::get('backgrounds', $filename);
    }

    public static function social(string $filename): string
    {
        return self::get('social', $filename);
    }

    public static function main(string $filename): string
    {
        return self::get('main', $filename);
    }

    public static function icon(string $type, string $filename): string
    {
        return self::get('icons', $filename, $type);
    }

    public static function page(string $page, string $filename): string
    {
        return self::get('pages', $filename, $page);
    }
}
