<?php

namespace App\Models\Users;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Crypt;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

use function App\Utilities\random_id;

class User extends Authenticatable
{
	use HasApiTokens, HasFactory, Notifiable, HasRoles;

	protected $with = ["billing_info"];
	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array<int, string>
	 */
	protected $fillable = ["first_name", "last_name", "email", "phone_number", "gender", "qualification", "password"];

	/**
	 * The attributes that should be hidden for serialization.
	 *
	 * @var array<int, string>
	 */
	protected $hidden = ["password", "access_token"];

	/**
	 * The attributes that should be cast.
	 *
	 * @var array<string, string>
	 */
	protected $casts = [
		"email_verified_at" => "datetime",
		"password" => "hashed",
	];

	public function accessToken(): Attribute
	{
		return Attribute::make(set: fn($value) => !empty($value) ? Crypt::encryptString($value) : $value);
	}

	public function accountId(): Attribute
	{
		return Attribute::set(fn($value) => random_id());
	}

	// ------------------> [Relationships]

	public function billing_info()
	{
		return $this->hasOne(BillingInfo::class, "account_id", "account_id");
	}
}
