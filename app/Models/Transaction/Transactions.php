<?php

namespace App\Models\Transaction;

use App\Models\Courses\Courses;
use App\Models\Users\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transactions extends Model
{
	use HasFactory;

	public function course()
	{
		return $this->belongsTo(Courses::class, "course_id", "course_id");
	}

	public function user()
	{
		return $this->belongsTo(User::class, "account_id", "account_id");
	}
}
