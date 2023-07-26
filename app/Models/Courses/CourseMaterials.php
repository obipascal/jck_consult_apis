<?php

namespace App\Models\Courses;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CourseMaterials extends Model
{
	use HasFactory;

	public function file(): Attribute
	{
		$baseUrl = config("app.url");

		return Attribute::get(fn($value) => !empty($value) ? "{$baseUrl}/{$value}" : $value);
	}
}
