<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

use function App\Utilities\esc;

class EscapeRequestParams
{
	/**
	 * Handle an incoming request.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @param  \Closure  $next
	 * @return mixed
	 */
	public function handle(Request $request, Closure $next)
	{
		foreach (esc($request->all()) as $key => $value) {
			$request->request->set($key, $value);
		}

		return $next($request);
	}
}