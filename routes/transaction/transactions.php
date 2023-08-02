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
			 * @todo Installment collections
			 * @api /api/v1/transactions/installment_collection/:id
			 */
			Route::post("installment_collection/{id}", "installment");
			/**
			 * @todo Fetch user transactions
			 * @api api/v1/transactions/user
			 */
			Route::get("user", "user");
			/**
			 * @todo Request Payent
			 * @api api/v1/transactions/request_payment/:id
			 */
			Route::get("request_payment/{id}", "requestPayment")->middleware("adminOnly");
		});

		/**
		 * @todo REST API Endpoints
		 * @api /api/v1/transactions
		 */
		Route::apiResource("transactions", TransactionsApi::class)->only(["index", "show"]);
	});
});