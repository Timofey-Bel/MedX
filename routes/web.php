<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ShowcaseController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\MainShowcaseController;

// Главная страница (showcase)
Route::get('/', [ShowcaseController::class, 'index'])->name('showcase');

// Страница входа
Route::get('/login', [LoginController::class, 'index'])->name('login');

// Страница main_showcase (база знаний)
Route::get('/main_showcase', [MainShowcaseController::class, 'index'])->name('main_showcase');

