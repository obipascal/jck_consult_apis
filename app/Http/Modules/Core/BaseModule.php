<?php namespace App\Http\Modules\Core;

use Illuminate\Http\Request;
use stdClass;

/**
 * The base Module file holds processing information such as
 * data to be processed and stored in dataase.
 */
trait BaseModule
{
	use DBModuleDrivers;
	public string $reason = "";
	protected string $appName;
}