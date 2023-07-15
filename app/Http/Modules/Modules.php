<?php namespace App\Http\Modules;

use JCKCon\Http\Modules\Courses\CoursesModule;
use JCKCon\Http\Modules\Settings\SettingsModule;
use JCKCon\Http\Modules\Users\UsersModule;

class Modules
{
	public static function User()
	{
		return new UsersModule();
	}

	public static function Settings()
	{
		return new SettingsModule();
	}

	public static function Courses()
	{
		return new CoursesModule();
	}
}
