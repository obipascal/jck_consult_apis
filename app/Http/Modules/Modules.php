<?php namespace App\Http\Modules;

use JCKCon\Http\Handlers\Analysis\AnalysisHandler;
use JCKCon\Http\Modules\Analysis\AnalysisModule;
use JCKCon\Http\Modules\Courses\CoursesModule;
use JCKCon\Http\Modules\Misc\MiscModule;
use JCKCon\Http\Modules\Promo\PromoCodesModule;
use JCKCon\Http\Modules\Reviews\ReviewsModule;
use JCKCon\Http\Modules\Settings\SettingsModule;
use JCKCon\Http\Modules\Transaction\TransactionModule;
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

	public static function Misc()
	{
		return new MiscModule();
	}

	public static function Trans()
	{
		return new TransactionModule();
	}

	public static function Reviews()
	{
		return new ReviewsModule();
	}

	public static function Analysis()
	{
		return new AnalysisModule();
	}
}