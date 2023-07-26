<?php

use App\Http\Controllers\Transaction\TransactionsApi;
use Illuminate\Support\Facades\Route;

Route::prefix("v1")->group(function () {
	Route::group(["middleware" => ["auth:sanctum"], "controller" => TransactionsApi::class, "prefix" => "transactions"], function () {
		/**
		 * @todo checkout a course enrollment
		 * @api /api/v1/transactions/checkout
		 */
		Route::post("checkout", "store");
	});
});