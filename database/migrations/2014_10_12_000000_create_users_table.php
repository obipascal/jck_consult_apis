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
		Schema::create("users", function (Blueprint $table) {
			$table->id();
			$table->unsignedBigInteger("account_id")->unique();
			$table->string("first_name")->nullable();
			$table->string("last_name")->nullable();
			$table->string("email")->unique();
			$table->string("phone_number")->nullable();
			$table->enum("gender", ["male", "female", "others"])->nullable();
			$table->enum("qualification", ["undergraduate", "graduate", "postgraduate"])->nullable();
			$table->string("password")->nullable();

			$table->text("access_token")->nullable();
			$table->timestamp("email_verified_at")->nullable();

			$table->timestamps();
		});
	}

	/**
	 * Reverse the migrations.
	 */
	public function down(): void
	{
		Schema::dropIfExists("users");
	}
};