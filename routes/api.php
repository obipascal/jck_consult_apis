<?php

use App\Http\Controllers\Courses\CourseMaterialsApi;
use App\Http\Controllers\Courses\CoursesApis;
use App\Http\Controllers\Misc\EnquiriesApi;
use App\Http\Controllers\Promos\PromotionApis;
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
			 * @todo Fetch user profile
			 * @api /api/v1/account/
			 */
			Route::get("/", "user");

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

			/**
			 * @todo Update user
			 * @api /api/v1/account
			 */
			Route::put("/", "updateUser");
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
	Route::group(["prefix" => "settings", "controller" => SettingsApi::class, "middleware" => ["auth:sanctum", "adminOnly"]], function () {
		/**
		 * @todo Fetch webiste configs
		 * @api /api/v1/settings
		 */
		Route::get("/", "index")->withoutMiddleware(["auth:sanctum", "adminOnly"]);

		/**
		 * @todo Create application settings
		 * @api /api/v1/settings
		 */
		Route::post("/", "store");
		/**
		 * @todo Upload website logo
		 * @api /api/v1/settings/logo
		 */
		Route::post("logo", "updateLogo");
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
			Route::get("/", "indexFAQ")->withoutMiddleware(["auth:sanctum", "adminOnly"]);
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

	/* Promotion Apis */
	Route::group(["middleware" => ["auth:sanctum", "adminOnly"], "controller" => PromotionApis::class], function () {
		/**
		 * @todo Custom endpoints defination
		 */
		Route::group(["prefix" => "promotions"], function () {
			/**
			 * @todo Apply promotion code
			 * @api /api/v1/promotions/apply
			 */
			Route::post("apply", "applyCode")->withoutMiddleware(["adminOnly"]);
		});
		/**
		 * @todo REST Endpoints
		 * @api /api/v1/promotions
		 */
		Route::apiResource("promotions", PromotionApis::class);
	});

	/* Enquiries  */
	Route::group(["middleware" => ["auth:sanctum", "adminOnly"], "controller" => EnquiriesApi::class], function () {
		/**
		 * @todo Custom endpoints
		 */
		Route::group(["prefix" => "enquiries"], function () {
			/**
			 * @todo handle make enquiry be users
			 * @api /api/v1/enquiries
			 */
			Route::post("/", "store")->withoutMiddleware(["auth:sanctum", "adminOnly"]);
		});
		/**
		 * @todo Enquiry rest endpoints
		 * @api /api/v1/enquiries
		 */
		Route::apiResource("enquiries", EnquiriesApi::class)->only(["index", "show", "destroy"]);
	});

	/* Course Material Apis */
	Route::group(["middleware" => ["auth:sanctum", "adminOnly"], "controller" => CourseMaterialsApi::class], function () {
		/** Custom endpoints  */
		Route::group(["prefix" => "materials"], function () {
			/**
			 * @todo Update material file
			 * @api /api/v1/materials/:id
			 */
			Route::post("/{id}", "updateMaterialFile")->whereNumber("id");
		});
		/**
		 * @todo REST Api endpoints
		 * @api /api/v1/materials
		 */
		Route::apiResource("materials", CourseMaterialsApi::class);
	});
});
