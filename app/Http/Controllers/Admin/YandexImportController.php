<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class YandexImportController extends Controller
{
    public function index()
    {
        return 'Hello from BannerController index!'; // Временно, для проверки
        // return view('admin.banners.index'); // Позже будем использовать Blade-шаблоны
    }
}
