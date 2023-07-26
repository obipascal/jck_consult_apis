<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
	/**
	 * Run the migrations.
	 */
	public function up(): void
	{
		Schema::create("enquiries", function (Blueprint $table) {
			$table->id();
			$table->unsignedBigInteger("contact_id")->unique();
			$table
				->foreignId("course_id")
				->nullable()
				->constrained("courses", "course_id")
				->cascadeOnUpdate()
				->cascadeOnDelete();

			$table->string("subject");
			$table->string("first_name");
			$table->string("last_name");
			$table->string("email");
			$table->string("phone_number");
			$table->text("message");
			$table->timestamps();
		});
	}

	/**
	 * Reverse the migrations.
	 */
	public function down(): void
	{
		Schema::dropIfExists("enquiries");
	}
};
