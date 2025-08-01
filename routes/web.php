<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('menu');
});

Route::get('/menu', [App\Http\Controllers\MenuController::class, 'index'])->name('menu');
route::get('/cart', [App\Http\Controllers\MenuController::class, 'cart'])->name('cart');
Route::post('/cart/add', [App\Http\Controllers\MenuController::class, 'addToCart'])->name('cart.add');


Route::get('/checkout', function () {
    return view('customer.checkout');
})->name('checkout');
