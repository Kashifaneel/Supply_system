<?php

use App\Http\Controllers\PurchaseOrderController;
use App\Http\Controllers\SupplyController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\UserController;
use App\Http\Middleware\AdminMiddleware;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome');
})->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    // User Management (Admin Only)
    Route::middleware([AdminMiddleware::class])->group(function () {
        Route::resource('users', UserController::class);
        Route::get('users-summary', [UserController::class, 'summary'])->name('users.summary');
    });
    Route::get('dashboard', function () {
        $user = auth()->user();
        
        $stats = [
            'total_pos' => \App\Models\PurchaseOrder::when($user->role !== 'Admin', function ($query) use ($user) {
                return $query->where('user_id', $user->id);
            })->count(),
            'pending_pos' => \App\Models\PurchaseOrder::where('status', 'Pending')
                ->when($user->role !== 'Admin', function ($query) use ($user) {
                    return $query->where('user_id', $user->id);
                })->count(),
            'total_supplies' => \App\Models\Supply::when($user->role !== 'Admin', function ($query) use ($user) {
                return $query->where('user_id', $user->id);
            })->count(),
            'pending_payments' => \App\Models\Payment::where('status', 'Pending')
                ->when($user->role !== 'Admin', function ($query) use ($user) {
                    return $query->whereHas('supply', function ($q) use ($user) {
                        $q->where('user_id', $user->id);
                    });
                })->count(),
        ];
        
        return Inertia::render('Dashboard', [
            'stats' => $stats
        ]);
    })->name('dashboard');

    // Purchase Orders
    Route::resource('purchase-orders', PurchaseOrderController::class);

    // Supplies
    Route::resource('supplies', SupplyController::class)->except(['edit', 'update', 'destroy']);
    Route::post('supplies/{supply}/upload-stamped', [SupplyController::class, 'uploadStamped'])
        ->name('supplies.upload-stamped');

    // Payments
    Route::resource('payments', PaymentController::class)->only(['index', 'create', 'store', 'show']);
    Route::post('payments/{payment}/confirm', [PaymentController::class, 'confirm'])
        ->name('payments.confirm');
    Route::delete('payments/{payment}/reject', [PaymentController::class, 'reject'])
        ->name('payments.reject');
});

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
