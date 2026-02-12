<?php

use App\Http\Controllers\CheckoutController;

Route::get('/', function () { return view('welcome'); }); // Rota padrão
Route::post('/checkout', [CheckoutController::class, 'store']); // Nossa rota