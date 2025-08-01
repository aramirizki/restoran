<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MenuController;

Route::get('/menu', [App\Http\Controllers\MenuController::class, 'index'])->name('menu');

Route::get('/', function () {
    return redirect()->route('menu');
});




Route::get('/cart', [MenuController::class, 'cart'])->name('cart');
Route::post('/cart/add', [MenuController::class, 'addToCart'])->name('cart.add');
Route::post('/cart/remove', [MenuController::class, 'removeFromCart'])->name('cart.remove');
Route::get('/cart/clear', [MenuController::class, 'clearCart'])->name('cart.clear');
Route::post('/cart/update', [MenuController::class, 'update'])->name('cart.update');

Route::get('/checkout', [MenuController::class, 'checkout'])->name('checkout');
Route::post('/checkout/store', [MenuController::class, 'store'])->name('checkout.store');
Route::get('/checkout/success/{orderId}', [MenuController::class, 'orderSuccess'])->name('checkout.success');

// Route::get('/checkout', function () {
//     return view('customer.checkout');
// })->name('checkout');
