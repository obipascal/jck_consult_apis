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
		Schema::create("customer_reviews", function (Blueprint $table) {
			$table->id();
			$table->unsignedBigInteger("review_id")->unique();
			$table->string("reviewer_name");
			$table->string("reviewer_email")->unique();
			$table->string("reviewer_role")->default("Customer");
			$table->string("reviewer_company")->default("JCK Consulting.");
			$table->string("reviewer_image")->nullable();
			$table->string("reviewer_message");
			$table->enum("status", ["moderation", "published"])->default("moderation");
			$table->timestamps();
		});
	}

	/**
	 * Reverse the migrations.
	 */
	public function down(): void
	{
		Schema::dropIfExists("customer_reviews");
	}
};
