<?php namespace App\Http\Modules;

use JCKCon\Http\Modules\Courses\CoursesModule;
use JCKCOn\Http\Modules\Promo\PromoCodesModule;
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

	public static function Promo()
	{
		return new PromoCodesModule();
	}
}
