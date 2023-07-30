<?php

/* Course APIs */

use App\Http\Controllers\Courses\CoursesApis;
use Illuminate\Support\Facades\Route;

Route::group(["prefix" => "v1", "middleware" => ["auth:sanctum", "adminOnly"], "controller" => CoursesApis::class], function () {
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
		Route::post("/{id}", "updateImage")->whereNumber("id");
		/**
		 * @todo View coures
		 * @api /api/v1/courses/:id
		 */
		Route::get("/{id}", "show")->withoutMiddleware(["auth:sanctum", "adminOnly"]);
		/**
		 * @todo Fetch all user courses
		 * @api /api/v1/courses/user/enrolled
		 */
		Route::get("user/enrolled", "userEnrolledCourses")->withoutMiddleware(["adminOnly"]);
	});
	/**
	 * @todo REST Endpoints
	 * @api /api/v1/courses
	 */
	Route::apiResource("courses", CoursesApis::class)->only(["index", "store", "update", "destroy"]);
});
