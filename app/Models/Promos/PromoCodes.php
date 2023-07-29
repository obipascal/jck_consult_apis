<?php

namespace App\Models\Promos;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PromoCodes extends Model
{
	use HasFactory;

	public function validFrom(): Attribute
	{
		return Attribute::make(set: fn($value) => !empty($value) ? Carbon::createFromDate($value)->toDateTimeString() : $value);
	}

	public function validTo(): Attribute
	{
		return Attribute::make(set: fn($value) => !empty($value) ? Carbon::createFromDate($value)->toDateTimeString() : $value);
	}
}
