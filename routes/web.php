<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ShowcaseController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\MainShowcaseController;
use App\Http\Controllers\MainSettingsController;
use App\Http\Controllers\DesignSettingsController;
use App\Http\Controllers\SupportSettingsController;
use App\Http\Controllers\MainTestsController;
use App\Http\Controllers\MainCommunityController;
use App\Http\Controllers\MainCommunityRulesController;
use App\Http\Controllers\MainConfidentialityController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\AboutController;
use App\Http\Controllers\FaqController;
use App\Http\Controllers\MainBaseController;
use App\Http\Controllers\MainBaseClinicalController;

// Главная страница (showcase)
Route::get('/', [ShowcaseController::class, 'index'])->name('showcase');

// Аутентификация
Route::get('/login', [LoginController::class, 'index'])->name('login');
Route::get('/register', [RegisterController::class, 'index'])->name('register');

// Главные страницы (main_*)
Route::get('/main_showcase', [MainShowcaseController::class, 'index'])->name('main_showcase');
Route::get('/main_settings', [MainSettingsController::class, 'index'])->name('main_settings');
Route::get('/design_settings', [DesignSettingsController::class, 'index'])->name('design_settings');
Route::get('/support_settings', [SupportSettingsController::class, 'index'])->name('support_settings');
Route::get('/main_tests', [MainTestsController::class, 'index'])->name('main_tests');
Route::get('/main_community', [MainCommunityController::class, 'index'])->name('main_community');
Route::get('/main_community_rules', [MainCommunityRulesController::class, 'index'])->name('main_community_rules');
Route::get('/main_confidentiality', [MainConfidentialityController::class, 'index'])->name('main_confidentiality');
Route::get('/main_base', [MainBaseController::class, 'index'])->name('main_base');
Route::get('/main_base_clinical', [MainBaseClinicalController::class, 'index'])->name('main_base_clinical');

// Информационные страницы
Route::get('/profile', [ProfileController::class, 'index'])->name('profile');
Route::get('/about', [AboutController::class, 'index'])->name('about');
Route::get('/faq', [FaqController::class, 'index'])->name('faq');

Route::fallback(function () {
    return response()->view('errors.404', [], 404);
});