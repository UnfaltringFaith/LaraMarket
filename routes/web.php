<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/', \App\Livewire\HomePage::class)->name('home');

Route::get('/categories', \App\Livewire\CategoriesPage::class)->name('categories');

Route::get('/products/', \App\Livewire\ProductsPage::class)->name('products');

Route::get('/products/{slug}', \App\Livewire\ProductPage::class)->name('product');

Route::get('cart', \App\Livewire\CartPage::class)->name('cart');

// Webhook для YooKassa (без CSRF и auth middleware)
Route::post('/payment/webhook', [\App\Http\Controllers\PaymentWebhookController::class, 'handle'])
    ->name('payment.webhook')->withoutMiddleware([\App\Http\Middleware\VerifyCsrfToken::class, 'auth']);

// Тестовая страница для webhook
Route::get('/webhook-test', function () {
    return view('webhook-test');
})->name('webhook.test');


Route::middleware(['auth'])->group(function () {
    Route::get('logout', function () {
        Auth::logout();
        return redirect()->route('home')->with('success', 'You have been logged out successfully!');
    })->name('logout');

    Route::get('checkout', \App\Livewire\CheckoutPage::class)->name('checkout');
    Route::get('orders', \App\Livewire\MyOrdersPage::class)->name('orders');
    Route::get('orders/{order}', \App\Livewire\MyOrderPage::class)->name('order');

    Route::get('success', \App\Livewire\SuccessPage::class)->name('success');
    Route::get('cancel', \App\Livewire\CancelPage::class)->name('cancel');
});

Route::middleware('guest')->group(function () {

    Route::get('login', \App\Livewire\Auth\LoginPage::class)->name('login');
    Route::get('register', \App\Livewire\Auth\RegisterPage::class)->name('register');
    Route::get('forgot', \App\Livewire\Auth\ForgotPasswordPage::class)->name('forgot-password');
    Route::get('reset/{token}', \App\Livewire\Auth\ResetPasswordPage::class)->name('password.reset');
});
