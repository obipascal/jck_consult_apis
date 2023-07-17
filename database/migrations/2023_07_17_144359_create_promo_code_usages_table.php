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
		Schema::create("promo_code_usages", function (Blueprint $table) {
			$table->id();
			$table
				->foreignId("promo_id")
				->constrained("promo_codes", "promo_id")
				->cascadeOnUpdate()
				->cascadeOnDelete();
			$table
				->foreignId("account_id")
				->constrained("users", "account_id")
				->cascadeOnUpdate()
				->cascadeOnDelete();

			$table->float("applied_amount")->default(0);

			$table->enum("status", ["applied", "used", "expired"])->default("applied");

			$table->timestamps();
		});
	}

	/**
	 * Reverse the migrations.
	 */
	public function down(): void
	{
		Schema::dropIfExists("promo_code_usages");
	}
};
