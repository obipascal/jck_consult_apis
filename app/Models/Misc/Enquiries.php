<?php

namespace App\Models\Misc;

use App\Models\Courses\Courses;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Enquiries extends Model
{
	use HasFactory;

	protected $with = ["course"];

	public function course()
	{
		return $this->belongsTo(Courses::class, "course_id", "course_id");
	}
}
