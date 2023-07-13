<?php

namespace App\Http\Middleware\Authentication;

use Closure;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Log;

/**
 * Decrypt the Sanctum token and passs it to sanctum for verification
 */
class DecryptToken
{
	/**
	 * Handle an incoming request.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
	 * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
	 */
	public function handle(Request $request, Closure $next)
	{
		try {
			$authorization = $request->headers->get("authorization");
			if (!empty($authorization)) {
				/* check if authorization contains bearer token  */
				$token = explode(" ", $authorization);
				if (isset($token[1])) {
					/* decrypt token and set it back */
					$decryptedToken = strlen($token[1]) > 100 ? Crypt::decryptString($token[1]) : $token[1];
					$request->headers->set("authorization", "Bearer {$decryptedToken}");
				}
			}
		} catch (Exception $th) {
			/* Log the error */
			Log::error($th->getMessage(), ["middleware" => "Sanctum Token Decrytor"]);

			/* continue the request  */
			return $next($request);
		}

		return $next($request);
	}
}