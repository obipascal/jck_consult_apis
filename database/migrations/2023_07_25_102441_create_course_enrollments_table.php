<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\Rules\Unique;

return new class extends Migration {
	/**
	 * Run the migrations.
	 */
	public function up(): void
	{
		Schema::create("course_enrollments", function (Blueprint $table) {
			$table->id();
			$table->unsignedBigInteger("enrollment_id")->unique();
			$table
				->foreignId("trans_id")
				->constrained("transactions", "trans_id")
				->cascadeOnUpdate()
				->cascadeOnDelete();
			$table
				->foreignId("account_id")
				->constrained("users", "account_id")
				->cascadeOnUpdate()
				->cascadeOnDelete();
			$table
				->foreignId("course_id")
				->constrained("courses", "course_id")
				->cascadeOnUpdate()
				->cascadeOnDelete();

			$table->timestamps();
		});
	}

	/**
	 * Reverse the migrations.
	 */
	public function down(): void
	{
		Schema::dropIfExists("course_enrollments");
	}
};
