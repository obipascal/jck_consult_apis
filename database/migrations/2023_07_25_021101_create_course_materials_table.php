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
		Schema::create("course_materials", function (Blueprint $table) {
			$table->id();
			$table
				->foreignId("course_id")
				->constrained("courses", "course_id")
				->cascadeOnUpdate()
				->cascadeOnDelete();

			$table->unsignedBigInteger("material_id")->unique();
			$table->string("title");
			$table->string("type");
			$table->string("size");
			$table->string("file");
			$table->timestamps();
		});
	}

	/**
	 * Reverse the migrations.
	 */
	public function down(): void
	{
		Schema::dropIfExists("course_materials");
	}
};
