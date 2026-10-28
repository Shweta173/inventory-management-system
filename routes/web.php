<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\InwardOutwardController;
use App\Http\Controllers\MaterialController;
use Illuminate\Support\Facades\Route;

// ── Auth (guest only) ─────────────────────────────────────────────────────────
Route::middleware('guest')->group(function () {
    Route::get('/login',    [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login',   [AuthController::class, 'login'])->name('login.post');
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register',[AuthController::class, 'register'])->name('register.post');
});

Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

// ── Protected routes ──────────────────────────────────────────────────────────
Route::middleware('auth')->group(function () {

    // Dashboard
    Route::get('/', fn () => view('dashboard'))->name('dashboard');

    // Categories
    Route::get('/categories',         [CategoryController::class, 'index'])->name('categories.index');
    Route::post('/categories/list',   [CategoryController::class, 'category_list'])->name('cat.lst');
    Route::post('/categories/save',   [CategoryController::class, 'category_save'])->name('cat.sv');
    Route::post('/categories/edit',   [CategoryController::class, 'category_edit'])->name('cat.edt');
    Route::post('/categories/delete', [CategoryController::class, 'category_delete'])->name('cat.del');

    // Materials
    Route::get('/materials',              [MaterialController::class, 'index'])->name('materials.index');
    Route::get('/materials/manage',       [MaterialController::class, 'manage'])->name('materials.manage');
    Route::post('/materials/list',        [MaterialController::class, 'material_list'])->name('mat.lst');
    Route::post('/materials/manage-list', [MaterialController::class, 'manage_list'])->name('mat.manage.lst');
    Route::post('/materials/save',        [MaterialController::class, 'material_save'])->name('mat.sv');
    Route::post('/materials/edit',        [MaterialController::class, 'material_edit'])->name('mat.edt');
    Route::post('/materials/delete',      [MaterialController::class, 'material_delete'])->name('mat.del');

    // Inward / Outward
    Route::get('/inward-outward',                       [InwardOutwardController::class, 'index'])->name('inward_outward.index');
    Route::post('/inward-outward/list',                 [InwardOutwardController::class, 'inward_outward_list'])->name('io.lst');
    Route::post('/inward-outward/save',                 [InwardOutwardController::class, 'inward_outward_save'])->name('io.sv');
    Route::get('/inward-outward/materials-by-category', [InwardOutwardController::class, 'get_materials_by_category'])->name('io.materials_by_category');
});
