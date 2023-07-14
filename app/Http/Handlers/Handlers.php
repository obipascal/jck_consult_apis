<?php namespace App\Http\Handlers;

use Illuminate\Http\Request;
use JCKCon\Http\Handlers\Settings\SettingsHandler;
use JCKCon\Http\Handlers\Users\UsersHandler;

class Handlers
{
	public static function Users(Request $request)
	{
		return new UsersHandler($request);
	}

	public static function Settings(Request $request)
	{
		return new SettingsHandler($request);
	}
}
