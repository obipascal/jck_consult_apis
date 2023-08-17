<?php

use App\Http\Controllers\Analysis\AnalysisApis;
use Illuminate\Support\Facades\Route;

Route::group(["prefix" => "v1", "controller" => AnalysisApis::class], function () {
	/**
	 * @todo Analysis rest
	 * @api /api/v1/analysis
	 */
	Route::apiResource("analysis", AnalysisApis::class);
});