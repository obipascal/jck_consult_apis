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
		Schema::create("transactions", function (Blueprint $table) {
			$table->id();
			$table->unsignedBigInteger("trans_id")->unique();
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

			$table->float("amount")->default(0);
			$table->float("discount")->default(0);
			$table->string("reference")->unique();
			$table->enum("status", ["pending", "success", "failed", "error"])->default("pending");
			$table->string("pi_id");
			$table->string("cs_code");
			$table->timestamps();
		});
	}

	/**
	 * Reverse the migrations.
	 */
	public function down(): void
	{
		Schema::dropIfExists("transactions");
	}
};
