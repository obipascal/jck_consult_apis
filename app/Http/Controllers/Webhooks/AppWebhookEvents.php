<?php

namespace App\Http\Controllers\Webhooks;

use App\Http\Config\RESTResponse;
use App\Http\Controllers\Controller;
use App\Http\Handlers\Handlers;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class AppWebhookEvents extends Controller
{
	use RESTResponse;

	/**
	 * Verify wallet topup transaction
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function stripe_webhook(Request $request)
	{
		$signingSecret = config("stripe.webhook_secret");
		$secretKey = config("stripe.secret_key");

		try {
			// The library needs to be configured with your account's secret key.
			// Ensure the key is kept out of any version control system you might be using.
			$stripe = new \Stripe\StripeClient($secretKey);

			$payload = @file_get_contents("php://input");

			try {
				$event = \Stripe\Event::constructFrom(json_decode($payload, true));
			} catch (\UnexpectedValueException $e) {
				// Invalid payload
				Log::error($e->getMessage(), ["file" => $e->getFile(), "line" => $e->getLine()]);
				return $this->terminateRequest("⚠️  Webhook error while parsing basic request.", $this->RESPONSE ?? $e->getMessage(), 400);
			}

			if ($signingSecret) {
				// Only verify the event if there is an endpoint secret defined
				// Otherwise use the basic decoded event
				$sig_header = $request->header("stripe-signature");

				try {
					$event = \Stripe\Webhook::constructEvent($payload, $sig_header, $signingSecret);
				} catch (\Stripe\Exception\SignatureVerificationException $e) {
					// Invalid signature

					Log::error($e->getMessage(), ["file" => $e->getFile(), "line" => $e->getLine()]);

					return $this->terminateRequest("Webhook error while validating signature.", $this->RESPONSE ?? $e->getMessage(), 400);
				}
			}

			// Handle the event
			switch ($event->type) {
				case "payment_intent.succeeded":
					/** @var \Stripe\PaymentIntent */
					$paymentIntent = $event->data->object;
					Handlers::Trans(request())->succeeded($paymentIntent);

					break;
				case "payment_method.attached":
					$paymentMethod = $event->data->object; // contains a \Stripe\PaymentMethod

					break;
				default:
					// Unexpected event type
					error_log("Received unknown event type");
			}

			/* Finally all went well return the response to caller/client */
			return $this->sendResponse(["body" => null, "type" => "webhooks"], "Thanks for the event Stripe!");
		} catch (Exception $th) {
			Log::error($th->getMessage(), ["file" => $th->getFile(), "line" => $th->getLine()]);

			return $this->terminateRequest("ERROR", $this->RESPONSE ?? $th->getMessage(), 500);
		}
	}
}
