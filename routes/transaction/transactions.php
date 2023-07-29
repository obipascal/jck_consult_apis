<?php

use App\Http\Controllers\Transaction\TransactionsApi;
use Illuminate\Support\Facades\Route;

Route::prefix("v1")->group(function () {
	Route::group(["middleware" => ["auth:sanctum"], "controller" => TransactionsApi::class], function () {
		Route::group(["prefix" => "transactions"], function () {
			/**
			 * @todo checkout a course enrollment
			 * @api /api/v1/transactions/checkout
			 */
			Route::post("checkout", "store");
			/**
			 * @todo Fetch user transactions
			 * @api api/v1/transactions/user
			 */
			Route::get("user", "user");
		});

		/**
		 * @todo REST API Endpoints
		 * @api /api/v1/transactions
		 */
		Route::apiResource("transactions", TransactionsApi::class)->only(["index", "show"]);
	});
});
