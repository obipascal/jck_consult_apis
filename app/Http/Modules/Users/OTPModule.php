<?php namespace JCKCon\Http\Modules\Users;

use App\Models\Users\OTPs;
use App\Models\Users\User;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Log;

use function App\Utilities\random_id;

trait OTPModule
{
	public function generateOTPCode(User $user): bool|string
	{
		try {
			$expiersIn = Carbon::now()
				->addHours(24)
				->toDateTimeString();
			$params["expires_in"] = $expiersIn;
			$params["otp_code"] = random_id(5);
			$params["email"] = $user->email;

			if (!$this->hasOTP($user->email)) {
				if (!$this->__save(new OTPs(), $params)) {
					return false;
				}
			} else {
				return $this->updateOTPCode($user);
			}

			return $params["otp_code"];
		} catch (Exception $th) {
			Log::error($th->getMessage(), ["Line" => $th->getLine(), "file" => $th->getFile()]);
			return false;
		}
	}

	public function updateOTPCode(User $user, bool $expire = false): bool|null|string
	{
		try {
			if (!($OTP = $this->getOTPUser($user->email))) {
				return false;
			}

			$expiersIn = Carbon::now()
				->addHours(24)
				->toDateTimeString();
			$params["expires_in"] = $expire === false ? $expiersIn : null;
			$params["otp_code"] = $expire === false ? random_id(5) : null;

			if (!$this->__update($OTP, "email", $user->email, $params)) {
				return false;
			}

			return $params["otp_code"];
		} catch (Exception $th) {
			Log::error($th->getMessage(), ["Line" => $th->getLine(), "file" => $th->getFile()]);
			return false;
		}
	}

	public function getOTPUser(string $email): bool|null|OTPs
	{
		try {
			return OTPs::query()
				->where("email", $email)
				->first();
		} catch (Exception $th) {
			Log::error($th->getMessage(), ["Line" => $th->getLine(), "file" => $th->getFile()]);
			return false;
		}
	}

	public function getOTPCode(string $email, string $otpCode): bool|null|OTPs
	{
		try {
			return OTPs::query()
				->where(["email" => $email, "otp_code" => $otpCode])
				->first();
		} catch (Exception $th) {
			Log::error($th->getMessage(), ["Line" => $th->getLine(), "file" => $th->getFile()]);
			return false;
		}
	}

	public function hasOTP(string $email): bool
	{
		try {
			return OTPs::query()
				->where("email", $email)
				->exists();
		} catch (Exception $th) {
			Log::error($th->getMessage(), ["Line" => $th->getLine(), "file" => $th->getFile()]);
			return false;
		}
	}
}
