<?php

use App\Http\Controllers\Webhooks\AppWebhookEvents;
use Illuminate\Support\Facades\Route;

Route::group(["prefix" => "webhk", "controller" => AppWebhookEvents::class], function () {
	/**
	 * @todo Listen to stripe webhook events
	 * @api /api/webhk/stripe
	 */
	Route::post("stripe", "stripe_webhook");
});
