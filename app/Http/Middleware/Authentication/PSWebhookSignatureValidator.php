<?php

namespace App\Http\Middleware\Authentication;

use App\Http\Config\RESTResponse;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PSWebhookSignatureValidator
{
	use RESTResponse;
	/**
	 * Handle an incoming request.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
	 * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
	 */
	public function handle(Request $request, Closure $next)
	{
		$paystackSignature = $request->headers->get("X-Paystack-Signature") ?? $request->headers->get("http_x_paystack_signature");

		$payload = @file_get_contents("php://input");

		if ($paystackSignature !== hash_hmac("sha512", $payload, config("paystack.secret"))) {
			Log::error("PS_WH_EVENT_LOG", ["error" => "request not validated", "signature" => $paystackSignature, "payload" => $payload]);

			return $this->terminateRequest("Request to this servers not authorized for this webhook event thank you!");
		}

		return $next($request);
	}
}