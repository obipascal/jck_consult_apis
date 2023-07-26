<?php

use App\Http\Controllers\Reviews\CustomerReviewsApi;
use Illuminate\Support\Facades\Route;

Route::prefix("v1")->group(function () {
	Route::group(["middleware" => ["auth:sanctum", "adminOnly"], "controller" => CustomerReviewsApi::class], function () {
		/* Public posting of reviews */
		Route::group(["prefix" => "reviews"], function () {
			/**
			 * @todo checkout a course enrollment
			 * @api /api/v1/reviews
			 */
			Route::post("/", "store")->withoutMiddleware(["auth:sanctum", "adminOnly"]);

			/**
			 * @todo get all published reviews
			 * @api /api/v1/reviews/reviews
			 */
			Route::get("reviews", "published")->withoutMiddleware(["auth:sanctum", "adminOnly"]);
		});

		/**
		 * @todo REST API endpoints
		 * @api /api/v1/reviews
		 */
		Route::apiResource("reviews", CustomerReviewsApi::class)->only(["index", "show", "update", "destroy"]);
	});
});
