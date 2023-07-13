<?php

use App\Http\Controllers\Users\UsersApis;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/
Route::prefix("v1")->group(function () {
	/* Account apis */
	Route::group(["prefix" => "account", "controller" => UsersApis::class], function () {
		/**
		 * @todo Create New account
		 * @api api/v1/account/create
		 */
		Route::post("create", "store");

		/* protected routes */
		Route::middleware("auth:sanctum")->group(function () {
			/**
			 * @todo Verify account
			 * @api /api/v1/account/verify
			 */
			Route::post("verify", "verify");
			/**
			 * @todo Update user profile
			 * @api /api/v1/account/:account_id
			 */
			Route::put("/{id}", "updateProfile");
			/**
			 * @todo Show user profile
			 * @api /api/v1/account/:account_id
			 */
			Route::get("/{id}", "show");
			/**
			 * @todo Update billing information
			 * @api /api/v1/account/billing/:account_id
			 */
			Route::get("billing/{id}", "billing");
			/**
			 * @todo Delete user account
			 * @api /api/v1/accountn/:account_id
			 */
			Route::delete("/{id}", "destory");
		});
		/**
		 * @todo Forget password
		 * @api /api/v1/account/forget_password
		 */
		Route::post("forget_password", "forgotPassword");
		/**
		 * @todo Confirm password reset
		 * @api /api/v1/account/fgpwd_confirm
		 */
		Route::post("fgpwd_confirm", "forgotPassword_confirmReset");
		/**
		 * @todo Reset confirm password
		 * @api /api/v1/account/fgpwd_reset
		 */
		Route::post("fgpwd_reset", "forgotPassword_resetPassword");
	});
});
