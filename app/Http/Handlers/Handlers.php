<?php namespace App\Http\Handlers;

use Illuminate\Http\Request;
use JCKCon\Http\Handlers\Users\UsersHandler;

class Handlers
{
	public static function Users(Request $request)
	{
		return new UsersHandler($request);
	}
}
