<?php

namespace App\Models\Courses;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Courses extends Model
{
	use HasFactory;

	public function image(): Attribute
	{
		$baseUrl = config("app.url");

		return Attribute::get(fn($value) => !empty($value) ? "{$baseUrl}/{$value}" : $value);
	}

	public function body(): Attribute
	{
		return Attribute::get(fn($value) => !empty($value) ? html_entity_decode($value) : $value);
	}
}
