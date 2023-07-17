<?php

namespace App\Http\Middleware;

use App\Http\Config\RESTResponse;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use JCKCon\Enums\APIResponseCodes;
use JCKCon\Enums\APIResponseMessages;
use Symfony\Component\HttpFoundation\Response;

class AdminOnlyMiddleware
{
	use RESTResponse;
	/**
	 * Handle an incoming request.
	 *
	 * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
	 */
	public function handle(Request $request, Closure $next): Response
	{
		if (!Gate::check("isAdmin")) {
			return $this->terminateRequest(APIResponseMessages::RES_UNAUTHORIZED->value, null, APIResponseCodes::UNAUTHORIZED->value);
		}
		return $next($request);
	}
}
