<?php

namespace App\Models\Courses;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CourseEnrollments extends Model
{
	use HasFactory;

	public function course()
	{
		return $this->belongsTo(Courses::class, "course_id", "course_id");
	}
}
