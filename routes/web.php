<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LandingController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\ManagerController;
use App\Http\Controllers\GuardController;
use App\Http\Controllers\CameraController;
use App\Http\Controllers\AlertController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\LogController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\ShiftController;

// PUBLIC ROUTES
Route::get('/', [LandingController::class, 'index'])->name('home');

Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register'])->middleware('throttle:3,1');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// ADMIN ROUTES
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function() {
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
    
    // User Management
    Route::get('/users', [AdminController::class, 'users'])->name('users.index');
    Route::get('/users/create', [AdminController::class, 'createUser'])->name('users.create');
    Route::post('/users/store', [AdminController::class, 'storeUser'])->name('users.store');
    Route::get('/users/edit/{id}', [AdminController::class, 'editUser'])->name('users.edit');
    Route::put('/users/update/{id}', [AdminController::class, 'updateUser'])->name('users.update');
    Route::delete('/users/delete/{id}', [AdminController::class, 'destroyUser'])->name('users.delete');

    // Pending approvals
    Route::get('/pending-users', [AdminController::class, 'pendingUsers'])->name('pending');
    Route::post('/users/approve/{id}', [AdminController::class, 'approveUser'])->name('users.approve');
    Route::post('/users/reject/{id}', [AdminController::class, 'rejectUser'])->name('users.reject');

    // Camera Management
    Route::get('/cameras', [CameraController::class, 'index'])->name('cameras.index');
    Route::get('/cameras/create', [CameraController::class, 'create'])->name('cameras.create');
    Route::post('/cameras/store', [CameraController::class, 'store'])->name('cameras.store');
    Route::get('/cameras/edit/{id}', [CameraController::class, 'edit'])->name('cameras.edit');
    Route::put('/cameras/update/{id}', [CameraController::class, 'update'])->name('cameras.update');
    Route::delete('/cameras/delete/{id}', [CameraController::class, 'destroy'])->name('cameras.delete');

    // Messages
    Route::get('/messages', [MessageController::class, 'adminIndex'])->name('messages.index');
    Route::post('/messages/send', [MessageController::class, 'send'])->name('messages.send');

    // Reports
    Route::get('/reports', [ReportController::class, 'adminReport'])->name('reports.index');
    Route::get('/reports/download', [ReportController::class, 'download'])->name('reports.download');

    // Shifts
    Route::get('/shifts', [ShiftController::class, 'index'])->name('shifts');
    Route::post('/shifts/update/{id}', [ShiftController::class, 'update'])->name('shifts.update');

    // Logs
    Route::get('/logs', [LogController::class, 'index'])->name('logs.index');
    Route::get('/logs/download', [LogController::class, 'download'])->name('logs.download');

    // Alerts
    Route::get('/alerts', [AlertController::class, 'adminIndex'])->name('alerts.index');
    Route::get('/alerts/{id}', [AlertController::class, 'show'])->name('alerts.show');
});

// MANAGER ROUTES
Route::middleware(['auth', 'manager', 'checkshift'])->prefix('manager')->name('manager.')->group(function() {
    Route::get('/dashboard', [ManagerController::class, 'dashboard'])->name('dashboard');
    Route::get('/alerts', [AlertController::class, 'managerIndex'])->name('alerts');
    Route::get('/alerts/create', [AlertController::class, 'managerCreate'])->name('alerts.create');
    Route::get('/alerts/{id}', [AlertController::class, 'show'])->name('alerts.show');
    Route::post('/alerts/instruct/{id}', [AlertController::class, 'instruct'])->name('alerts.instruct');
    Route::post('/alerts/resolve/{id}', [AlertController::class, 'resolve'])->name('alerts.resolve');
    Route::post('/alerts/raise', [AlertController::class, 'raise'])->name('alerts.raise');
    Route::get('/reports', [ReportController::class, 'managerReport'])->name('reports');
    Route::get('/messages', [MessageController::class, 'managerIndex'])->name('messages');
    Route::post('/messages/read/{id}', [MessageController::class, 'markRead'])->name('messages.read');
});

// GUARD ROUTES
Route::middleware(['auth', 'guard', 'checkshift'])->prefix('guard')->name('guard.')->group(function() {
    Route::get('/dashboard', [GuardController::class, 'dashboard'])->name('dashboard');
    Route::get('/alerts', [AlertController::class, 'guardIndex'])->name('alerts');
    Route::get('/alerts/create', [AlertController::class, 'guardCreate'])->name('alerts.create');
    Route::get('/alerts/resolve/{id}', [AlertController::class, 'showResolve'])->name('alerts.resolve.show');
    Route::post('/alerts/raise', [AlertController::class, 'raise'])->name('alerts.raise');
    Route::post('/alerts/emergency', [AlertController::class, 'emergency'])->name('alerts.emergency');
    Route::post('/alerts/resolve/{id}', [AlertController::class, 'resolve'])->name('alerts.resolve');
    Route::get('/messages', [MessageController::class, 'guardIndex'])->name('messages');
    Route::post('/messages/read/{id}', [MessageController::class, 'markRead'])->name('messages.read');
});

// AJAX ROUTES
Route::middleware(['auth'])->prefix('api')->name('api.')->group(function() {
    Route::get('/alerts/new', [AlertController::class, 'getNewAlerts'])->name('alerts.new');
    Route::post('/messages/read/{id}', [MessageController::class, 'markRead'])->name('messages.read');
    Route::get('/notifications', [NotificationController::class, 'getNew'])->name('notifications.new');
    Route::post('/notifications/read/{id}', [NotificationController::class, 'markRead'])->name('notifications.read');
});
