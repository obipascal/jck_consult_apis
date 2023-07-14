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
		Schema::create("site_settings", function (Blueprint $table) {
			$table->id();
			$table->unsignedBigInteger("site_id")->unique();
			$table->string("name")->nullable();
			$table->string("desc")->nullable();
			$table->string("email")->nullable();
			$table->string("phone_number")->nullable();
			$table->string("line_address")->nullable();
			$table->text("about")->nullable();
			$table->string("facebook_handle")->nullable();
			$table->string("instagram_handle")->nullable();
			$table->string("twitter_handle")->nullable();
			$table->string("linkedin_handle")->nullable();
			$table->string("whatsapp_handle")->nullable();
			$table->string("logo")->nullable();
			$table->timestamps();
		});
	}

	/**
	 * Reverse the migrations.
	 */
	public function down(): void
	{
		Schema::dropIfExists("site_settings");
	}
};
