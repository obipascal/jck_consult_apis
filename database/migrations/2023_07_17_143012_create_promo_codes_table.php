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
		Schema::create("promo_codes", function (Blueprint $table) {
			$table->id();
			$table->unsignedBigInteger("promo_id")->unique();

			$table->string("promo_code")->unique();
			$table->float("disc_percentage")->default(0);
			$table->dateTime("valid_from");
			$table->dateTime("valid_to");

			$table->timestamps();
		});
	}

	/**
	 * Reverse the migrations.
	 */
	public function down(): void
	{
		Schema::dropIfExists("promo_codes");
	}
};
