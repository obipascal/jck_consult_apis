<?php

use App\Http\Controllers\Analysis\AnalysisApis;
use Illuminate\Support\Facades\Route;

Route::group(["prefix" => "v1", "controller" => AnalysisApis::class, "middleware" => ["auth:sanctum", "adminOnly"]], function () {
	/**
	 * @todo Analysis rest
	 * @api /api/v1/analysis
	 */
	Route::apiResource("analysis", AnalysisApis::class);
});