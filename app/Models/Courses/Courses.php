<?php

namespace App\Models\Courses;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Courses extends Model
{
	use HasFactory;

	protected $with = ["materials"];

	public function image(): Attribute
	{
		$baseUrl = config("app.url");

		return Attribute::get(fn($value) => !empty($value) ? "{$baseUrl}/{$value}" : $value);
	}

	public function body(): Attribute
	{
		return Attribute::get(fn($value) => !empty($value) ? html_entity_decode($value) : $value);
	}

	public function materials()
	{
		return $this->hasMany(CourseMaterials::class, "course_id", "course_id");
	}
}
