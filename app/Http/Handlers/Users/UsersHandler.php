<?php namespace JCKCon\Http\Handlers\Users;

use App\Http\Handlers\Core\BaseHandler;
use App\Http\Modules\Modules;
use App\Mail\AccountVerification;
use App\Mail\ForgetPassword;
use App\Models\Users\User;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use JCKCon\Enums\APIResponseMessages;
use JCKCon\Enums\APIResponseCodes;
use JCKCon\Enums\UsersRoles;

class UsersHandler
{
	use BaseHandler;

	public function users()
	{
		try {
			$params = $this->request->all([""]);

			$User = $this->request->user();

			$responseData = DB::transaction(function () use ($params, $User) {
				if (!($Users = Modules::User()->all($User->account_id))) {
					throw new Exception(APIResponseMessages::DB_ERROR->value, APIResponseCodes::SERVER_ERR->value);
				}

				return $Users;
			}, attempts: 1);

			//-----------------------------------------------------

			/** Request response data */
			$responseMessage = "Success, users retrieved.";
			$response["type"] = "account";
			$response["body"] = $responseData;
			$responseCode = 200;

			return $this->response($response, $responseMessage, $responseCode);
		} catch (Exception $th) {
			Log::error($th->getMessage(), ["Line" => $th->getLine(), "file" => $th->getFile()]);

			return $this->raise($th->getMessage(), null, 400);
		}
	}
	// ------------------------> [Profile]
	public function create(): UsersHandler
	{
		try {
			DB::beginTransaction();

			$params = $this->request->all(["email"]);

			// Add the account id param so it will be prefilled
			$params["account_id"] = null; // this will be prefilled by the user modal

			if (!($otp_code = Modules::User()->add($params, UsersRoles::USER->value))) {
				return $this->raise(APIResponseMessages::DB_ERROR->value, null, APIResponseCodes::SERVER_ERR->value);
			}

			/* send user mail */
			Mail::to($params["email"])->send(new AccountVerification($otp_code));

			/* Get the newly created user  */
			$User = Modules::User()->get($params["email"]);

			$User->api_token = $User->access_token;

			//-----------------------------------------------------

			/** Request response data */
			$responseMessage = "Success, account created successfully!";
			$response["type"] = "account";
			$response["body"] = $User;
			$responseCode = 201;

			DB::commit();

			return $this->response($response, $responseMessage, $responseCode);
		} catch (Exception $th) {
			Log::error($th->getMessage(), ["Line" => $th->getLine(), "file" => $th->getFile()]);

			DB::rollBack();
			DB::commit();

			return $this->raise();
		}
	}

	public function verify(): UsersHandler
	{
		try {
			DB::beginTransaction();

			/** @var User */
			$User = $this->request->user();

			$params = $this->request->all(["otp_code"]);

			/* get the user information */
			if (!($OTPCode = Modules::User()->getOTPCode($User->email, $params["otp_code"]))) {
				return $this->raise(APIResponseMessages::DB_ERROR->value, null, APIResponseCodes::SERVER_ERR->value);
			}

			/* validate if the otp is still value  */
			$now = Carbon::now();
			$expiresin = Carbon::createFromDate($OTPCode->expires_in);
			if ($now > $expiresin) {
				/* resend user new account verification code  */
				if (!($otp_code = Modules::User()->generateOTPCode($User))) {
					return $this->raise(APIResponseMessages::OTP_GEN_ERR->value, null, APIResponseCodes::TECHNICAL_ERR->value);
				}

				Mail::to($User)->send(new AccountVerification($otp_code));

				$User = Modules::User()->get($User->account_id);
				$User->api_token = $User->access_token;

				/* response message */
				$responseMessage = "OTP Code has expired, please re-verify your account with the new code sent to your email.";
			} else {
				/* update user verification timestamp */
				$updateParams["email_verified_at"] = Carbon::now()->toDateTimeString();
				if (!Modules::User()->update($User->account_id, $updateParams)) {
					return $this->raise(APIResponseMessages::DB_ERROR->value, null, APIResponseCodes::SERVER_ERR->value);
				}

				$User = Modules::User()->get($User->account_id);
				$User->api_token = $User->access_token;

				/* response message */
				$responseMessage = "Success, account verified and confirmed successfully!";
			}

			//-----------------------------------------------------

			/** Request response data */

			$response["type"] = "account";
			$response["body"] = $User;
			$responseCode = 200;

			DB::commit();

			return $this->response($response, $responseMessage, $responseCode);
		} catch (Exception $th) {
			Log::error($th->getMessage(), ["Line" => $th->getLine(), "file" => $th->getFile()]);

			DB::rollBack();
			DB::commit();

			return $this->raise();
		}
	}

	public function updateProfile(string $id): UsersHandler
	{
		try {
			DB::beginTransaction();

			$params = $this->request->all(["first_name", "last_name", "phone_number", "gender", "qualification", "password"]);

			foreach ($params as $param => $value) {
				if (empty($value)) {
					unset($params[$param]);
				}
			}

			if (!Modules::User()->update($id, $params)) {
				return $this->raise(APIResponseMessages::DB_ERROR->value, null, APIResponseCodes::SERVER_ERR->value);
			}

			$User = Modules::User()->get($id);
			//-----------------------------------------------------

			/** Request response data */
			$responseMessage = "Success, profile updated successfully!";
			$response["type"] = "account";
			$response["body"] = $User;
			$responseCode = 200;

			DB::commit();

			return $this->response($response, $responseMessage, $responseCode);
		} catch (Exception $th) {
			Log::error($th->getMessage(), ["Line" => $th->getLine(), "file" => $th->getFile()]);

			DB::rollBack();
			DB::commit();

			return $this->raise();
		}
	}

	public function fetchProfile(string $id): UsersHandler
	{
		try {
			DB::beginTransaction();

			if (!($User = Modules::User()->get($id))) {
				return $this->raise(APIResponseMessages::NOT_FOUND->value, null, APIResponseCodes::NOT_FOUND->value);
			}

			$User->api_token = $User->access_token;
			$User->role = $User->getRoleNames()[0];
			unset($User->roles);
			//-----------------------------------------------------

			/** Request response data */
			$responseMessage = "Success, user retrieved";
			$response["type"] = "account";
			$response["body"] = $User;
			$responseCode = 200;

			DB::commit();

			return $this->response($response, $responseMessage, $responseCode);
		} catch (Exception $th) {
			Log::error($th->getMessage(), ["Line" => $th->getLine(), "file" => $th->getFile()]);

			DB::rollBack();
			DB::commit();

			return $this->raise();
		}
	}

	public function fetchUser(): UsersHandler
	{
		try {
			DB::beginTransaction();

			/** @var User */
			$User = $this->request->user();

			$User->api_token = $User->access_token;
			$User->role = $User->getRoleNames()[0];
			unset($User->roles);
			//-----------------------------------------------------

			/** Request response data */
			$responseMessage = "Success, user retrieved";
			$response["type"] = "account";
			$response["body"] = $User;
			$responseCode = 200;

			DB::commit();

			return $this->response($response, $responseMessage, $responseCode);
		} catch (Exception $th) {
			Log::error($th->getMessage(), ["Line" => $th->getLine(), "file" => $th->getFile()]);

			DB::rollBack();
			DB::commit();

			return $this->raise();
		}
	}

	public function updateUser(): UsersHandler
	{
		try {
			DB::beginTransaction();

			$params = $this->request->all(["first_name", "last_name", "phone_number", "gender", "qualification", "password"]);

			/** @var User */
			$User = $this->request->user();

			foreach ($params as $param => $value) {
				if (empty($value)) {
					unset($params[$param]);
				}
			}

			if (!Modules::User()->update($User->account_id, $params)) {
				return $this->raise(APIResponseMessages::DB_ERROR->value, null, APIResponseCodes::SERVER_ERR->value);
			}

			$User = Modules::User()->get($User->account_id);

			//-----------------------------------------------------

			/** Request response data */
			$responseMessage = "Success, profile updated successfully!";
			$response["type"] = "account";
			$response["body"] = $User;
			$responseCode = 200;

			DB::commit();

			return $this->response($response, $responseMessage, $responseCode);
		} catch (Exception $th) {
			Log::error($th->getMessage(), ["Line" => $th->getLine(), "file" => $th->getFile()]);

			DB::rollBack();
			DB::commit();

			return $this->raise();
		}
	}

	/**
	 * Create of not exists or update if exists billing information
	 *
	 * @param string $id
	 *
	 * @return UsersHandler
	 */
	public function billingInfo(string $id): UsersHandler
	{
		try {
			DB::beginTransaction();

			$params = $this->request->all(["city", "country", "house_no", "street"]);

			/* Add the account id param */
			$params["account_id"] = $id;
			if (!($BillingInfo = Modules::User()->addBillingInfo($id, $params))) {
				return $this->raise(APIResponseMessages::DB_ERROR->value, null, APIResponseCodes::SERVER_ERR->value);
			}

			//-----------------------------------------------------

			/** Request response data */
			$responseMessage = "Success, billing info updated successfully!";
			$response["type"] = "account";
			$response["body"] = $BillingInfo;
			$responseCode = 200;

			DB::commit();

			return $this->response($response, $responseMessage, $responseCode);
		} catch (Exception $th) {
			Log::error($th->getMessage(), ["Line" => $th->getLine(), "file" => $th->getFile()]);

			DB::rollBack();
			DB::commit();

			return $this->raise();
		}
	}

	public function deleteUser(string $id): UsersHandler
	{
		try {
			DB::beginTransaction();

			if (!Modules::User()->remove($id)) {
				return $this->raise(APIResponseMessages::ACCOUNT_404->value, null, APIResponseCodes::NOT_FOUND->value);
			}

			//-----------------------------------------------------

			/** Request response data */
			$responseMessage = "Success, user account deleted successfully!";
			$response["type"] = "";
			$response["body"] = null;
			$responseCode = 200;

			DB::commit();

			return $this->response($response, $responseMessage, $responseCode);
		} catch (Exception $th) {
			Log::error($th->getMessage(), ["Line" => $th->getLine(), "file" => $th->getFile()]);

			DB::rollBack();
			DB::commit();

			return $this->raise();
		}
	}

	public function authorized(): UsersHandler
	{
		try {
			DB::beginTransaction();

			$params = $this->request->all(["username", "password"]);

			/* get the user */
			/** @var User */
			if (!($User = Modules::User()->get($params["username"]))) {
				return $this->raise(APIResponseMessages::DB_ERROR->value, null, APIResponseCodes::SERVER_ERR->value);
			}

			// now validate user password
			if (!Hash::check($params["password"], $User->password)) {
				return $this->raise(APIResponseMessages::INVALID_PWD->value, null, APIResponseCodes::UNAUTHORIZED->value);
			}

			$User->api_token = $User->access_token;
			$User->role = $User->getRoleNames()[0];
			unset($User->roles);
			//-----------------------------------------------------

			/** Request response data */
			$responseMessage = "Success, account login successful";
			$response["type"] = "account";
			$response["body"] = $User;
			$responseCode = 200;

			DB::commit();

			return $this->response($response, $responseMessage, $responseCode);
		} catch (Exception $th) {
			Log::error($th->getMessage(), ["Line" => $th->getLine(), "file" => $th->getFile()]);

			DB::rollBack();
			DB::commit();

			return $this->raise();
		}
	}
	// -------------------> [Forget Password]

	public function forgotPassword(): UsersHandler
	{
		try {
			DB::beginTransaction();

			$params = $this->request->all(["email"]);

			if (!Modules::User()->exists($params["email"])) {
				return $this->raise(APIResponseMessages::ACCOUNT_404->value, null, APIResponseCodes::NOT_FOUND->value);
			}

			$User = Modules::User()->get($params["email"]);

			/* generate otp code */
			if (!($otp_code = Modules::User()->generateOTPCode($User))) {
				return $this->raise(APIResponseMessages::DB_ERROR->value, null, APIResponseCodes::SERVER_ERR->value);
			}

			/* send mail */
			Mail::to($User)->send(new ForgetPassword($otp_code));

			//-----------------------------------------------------

			/** Request response data */
			$responseMessage = "Great news! We have successfully sent your confirmation code to your email address. Kindly check your inbox to complete the verification process. Thank you!";
			$response["type"] = "account";
			$response["body"] = ["api_token" => $User->access_token];
			$responseCode = 200;

			DB::commit();

			return $this->response($response, $responseMessage, $responseCode);
		} catch (Exception $th) {
			Log::error($th->getMessage(), ["Line" => $th->getLine(), "file" => $th->getFile()]);

			DB::rollBack();
			DB::commit();

			return $this->raise();
		}
	}

	public function confirmPasswordReset(): UsersHandler
	{
		try {
			DB::beginTransaction();

			/** @var User */
			$User = $this->request->user();

			$params = $this->request->all(["otp_code"]);

			/* get the user information */
			if (!($OTPCode = Modules::User()->getOTPCode($User->email, $params["otp_code"]))) {
				return $this->raise(APIResponseMessages::DB_ERROR->value, null, APIResponseCodes::SERVER_ERR->value);
			}

			/* validate if the otp is still value  */
			$now = Carbon::now();
			$expiresin = Carbon::createFromDate($OTPCode->expires_in);
			if ($now > $expiresin) {
				/* resend user new account verification code  */
				if (!($otp_code = Modules::User()->generateOTPCode($User))) {
					return $this->raise(APIResponseMessages::OTP_GEN_ERR->value, null, APIResponseCodes::TECHNICAL_ERR->value);
				}

				Mail::to($User)->send(new ForgetPassword($otp_code));

				$User = Modules::User()->get($User->account_id);

				/* response message */
				$responseMessage = "OTP Code has expired, please check your email for a new verification code.";
			} else {
				// expirer the otp so user can not reuse it
				Modules::User()->updateOTPCode($User, true);
			}

			/* generate a reset password code and timed it */
			$resetToken = Carbon::now()
				->addMinutes(30)
				->toTimeString();
			$encryptedToken = Crypt::encryptString($resetToken);

			//-----------------------------------------------------

			/** Request response data */
			$responseMessage = "Success, password reset confirmed you can now reset your account password.";
			$response["type"] = "account";
			$response["body"] = ["reset_token" => $encryptedToken];
			$responseCode = 200;

			DB::commit();

			return $this->response($response, $responseMessage, $responseCode);
		} catch (Exception $th) {
			Log::error($th->getMessage(), ["Line" => $th->getLine(), "file" => $th->getFile()]);

			DB::rollBack();
			DB::commit();

			return $this->raise();
		}
	}

	public function resetPassword()
	{
		try {
			DB::beginTransaction();

			/** @var User */
			$User = $this->request->user();

			$params = $this->request->all(["reset_token", "password"]);

			/* verify the reset token  */
			if (!($token = Crypt::decryptString($params["reset_token"]))) {
				return $this->raise(APIResponseMessages::OPS_ABORTED->value, null, APIResponseCodes::CLIENT_ERR->value);
			}

			$now = Carbon::now();
			$tokenTimer = Carbon::createFromDate($token);

			if ($now > $tokenTimer) {
				return $this->raise(APIResponseMessages::PSWD_OPS_EXP->value, null, APIResponseCodes::CLIENT_ERR->value);
			}

			/* remove the reset token from params to update user password */
			unset($params["reset_token"]);
			if (!Modules::User()->update($User->account_id, $params)) {
				return $this->raise(APIResponseMessages::DB_ERROR->value, null, APIResponseCodes::SERVER_ERR->value);
			}

			// reset account access token
			$User = Modules::User()->resetAccessToken($User);

			//-----------------------------------------------------

			/** Request response data */
			$responseMessage = "Success, account password reset successfully!";
			$response["type"] = "account";
			$response["body"] = ["api_token" => $User->access_token];
			$responseCode = 200;

			DB::commit();

			return $this->response($response, $responseMessage, $responseCode);
		} catch (Exception $th) {
			Log::error($th->getMessage(), ["Line" => $th->getLine(), "file" => $th->getFile()]);

			DB::rollBack();
			DB::commit();

			return $this->raise();
		}
	}
}
