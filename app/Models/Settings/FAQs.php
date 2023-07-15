<?php

namespace App\Models\Settings;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FAQs extends Model
{
	use HasFactory;

	public function content(): Attribute
	{
		return Attribute::get(fn($value) => !empty($value) ? html_entity_decode($value) : $value);
	}
}