<?php

use App\Http\Controllers\AccountController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

// 首页
Route::get('/', function () {
    return view('index');
});

// 仪表盘
Route::get('/dashboard', function () {
    return view('dashboard');
});

// 账单管理
Route::get('/transactions', function () {
    return view('transaction');
});

// 统计分析
Route::get('/statistics', function () {
    return view('statistic');
});

// 分类管理
Route::get('/categories', function () {
    return view('category');
});

// 账户管理
Route::get('/accounts', [AccountController::class, 'page'])->middleware('auth');
Route::group(['prefix' => '/accounts'], function () {
    Route::get('delete/{accId}', [AccountController::class, 'delete'])->name('accounts.delete');
    Route::post('add', [AccountController::class, 'add'])->name('accounts.add');
});

// 个人中心
Route::get('/profile', function () {
    return view('profile');
})->middleware('auth');
Route::post('/user/avatar', [ProfileController::class, 'uploadAvatar'])->middleware('auth');
// 系统设置
Route::get('/settings', function () {
    return view('setting');
});
