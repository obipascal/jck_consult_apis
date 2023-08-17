<?php namespace App\Http\Handlers;

use Illuminate\Http\Request;
use JCKCon\Http\Handlers\Analysis\AnalysisHandler;
use JCKCon\Http\Handlers\Courses\CoursesHandler;
use JCKCon\Http\Handlers\Misc\MiscHandler;
use JCKCon\Http\Handlers\Promos\PromosHandler;
use JCKCon\Http\Handlers\Reviews\ReviewsHandler;
use JCKCon\Http\Handlers\Settings\SettingsHandler;
use JCKCon\Http\Handlers\Transaction\TransactionHandler;
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

	public static function Courses(Request $request)
	{
		return new CoursesHandler($request);
	}

	public static function Promos(Request $request)
	{
		return new PromosHandler($request);
	}

	public static function Misc(Request $request)
	{
		return new MiscHandler($request);
	}

	public static function Trans(Request $request)
	{
		return new TransactionHandler($request);
	}

	public static function Reviews(Request $request)
	{
		return new ReviewsHandler($request);
	}

	public static function Analysis(Request $request)
	{
		return new AnalysisHandler($request);
	}
}