<?php

use App\Http\Controllers\Courses\CoursesApis;
use App\Http\Controllers\Settings\SettingsApi;
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
		 * @todo authorize account access
		 * @api /api/v1/account/authorize
		 */
		Route::post("authorize", "authorizedAccountLogin");
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
			Route::put("/{id}", "update");
			/**
			 * @todo Show user profile
			 * @api /api/v1/account/:account_id
			 */
			Route::get("/{id}", "show");
			/**
			 * @todo Update billing information
			 * @api /api/v1/account/billing/:account_id
			 */
			Route::put("billing/{id}", "billing");
			/**
			 * @todo Delete user account
			 * @api /api/v1/accountn/:account_id
			 */
			Route::delete("/{id}", "destroy");
		});
		/**
		 * @todo Forget password
		 * @api /api/v1/account/forget_password
		 */
		Route::post("forget_password", "forgotPassword");
		Route::middleware("auth:sanctum")->group(function () {
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

	/* Settings API */
	Route::group(["prefix" => "settings", "controller" => SettingsApi::class, "middleware" => "auth:sanctum"], function () {
		/**
		 * @todo Create application settings
		 * @api /api/v1/settings
		 */
		Route::post("/", "store");
		/**
		 * @todo Fetch site settings
		 * @api /api/v1/settings/:site_id
		 */
		Route::get("/{id}", "show")->whereNumber("id");

		/* FAQs */
		Route::group(["prefix" => "faqs"], function () {
			/**
			 * @todo fetch all faqs
			 * @api /api/v1/settings/faqs
			 */
			Route::get("/", "indexFAQ");
			/**
			 * @todo Create a new faq
			 * @api /api/v1/settings/faqs
			 */
			Route::post("/", "storeFAQ");
			/**
			 * @todo Fetch a particular faq
			 * @api /api/v1/settings/faqs
			 */
			Route::get("/{id}", "showFAQ")->whereNumber("id");
			/**
			 * @todo Update an faq
			 * @api /api/v1/settings/faqs
			 */
			Route::put("/{id}", "updateFAQ");
			/**
			 * @todo remove an faq
			 * @api /api/v1/settings/faqs/:faq_id
			 */
			Route::delete("/{id}", "destroyFAQ");
		});
	});

	/* Course APIs */
	Route::group(["middleware" => ["auth:sanctum", "adminOnly"], "controller" => CoursesApis::class], function () {
		/**
		 * @todo None-REST Endpoints
		 */
		Route::group(["prefix" => "courses"], function () {
			/**
			 * @todo Fetch published coursed for display at home page and other none protected areas
			 * @api /api/v1/courses/active
			 */
			Route::get("active", "published")->withoutMiddleware(["auth:sanctum", "adminOnly"]);
			/**
			 * @todo Search for course.
			 * @api /api/v1/courses/search
			 */
			Route::get("search", "search")->withoutMiddleware(["auth:sanctum", "adminOnly"]);
			/**
			 * @todo Update course image
			 *
			 * @api /api/v1/courses/:id
			 */
			Route::post("/{id}", "updateImage");
			/**
			 * @todo View coures
			 * @api /api/v1/courses/:id
			 */
			Route::get("/{id}", "show")->withoutMiddleware(["auth:sanctum", "adminOnly"]);
		});
		/**
		 * @todo REST Endpoints
		 * @api /api/v1/courses
		 */
		Route::apiResource("courses", CoursesApis::class)->only(["index", "store", "update", "destroy"]);
	});
});
