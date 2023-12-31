<?php

namespace App\Models\Settings;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SiteSettings extends Model
{
	use HasFactory;

	public function logo(): Attribute
	{
		$baseUrl = config("app.url");

		return Attribute::get(fn($value) => !empty($value) ? "{$baseUrl}/{$value}" : $value);
	}

	public function about(): Attribute
	{
		return Attribute::get(fn($value) => !empty($value) ? html_entity_decode($value) : $value);
	}
}