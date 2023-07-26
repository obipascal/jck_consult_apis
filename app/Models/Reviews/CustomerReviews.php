<?php

namespace App\Models\Reviews;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomerReviews extends Model
{
	use HasFactory;

	public function reviewerImage(): Attribute
	{
		$baseUrl = config("app.url");

		return Attribute::get(fn($value) => !empty($value) ? "{$baseUrl}/{$value}" : $value);
	}
}
