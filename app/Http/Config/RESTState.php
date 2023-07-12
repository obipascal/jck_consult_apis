<?php namespace App\Http\Config;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;
use Illuminate\Support\Str;
/**
 * Helpers
 */
trait RESTState
{
	/**
	 * Get validation errors as an array of messages
	 *
	 * @param mixed $validation
	 *
	 * @return array|string
	 */
	public function getValidationMessages($validation)
	{
		try {
			if (!$validation) {
				throw new Exception("Check the param you supplied.");
			}

			$error_messages = [];
			foreach ($validation->errors()->messages() as $fieldFailed => $errorMsg) {
				foreach ($errorMsg as $message) {
					array_push($error_messages, $message);
				}
			}

			return $error_messages;
		} catch (Exception $th) {
			return $th->getMessage();
		}
	}

	/**
	 * Check if the provided password is strong enough
	 *
	 * @param string $password The password to be validated
	 * @param function $callback A callback function that get's the reason why the validation
	 * fails
	 *
	 * @return bool
	 */
	public function isPasswordStrong(string $password, $callback = null)
	{
		$strongPasswordRule = [
			"password" => [
				"bail",
				"required",
				Password::min(8)
					->mixedCase()
					->letters()
					->numbers()
					->symbols()
					->uncompromised(),
			],
		];

		$validator = Validator::make($password, $strongPasswordRule);

		if (!$callback) {
			if (is_callable($callback)) {
				call_user_func($callback, $validator->fails(), !$validator->fails() ? "" : $this->getValidationMessages($validator)[0]);
			}
		}

		return $validator->fails();
	}

	/**
	 * process profile photo upload
	 *
	 * @param \Illuminate\Http\Request $request
	 *
	 */
	public function processProfilePhotoUpload(Request $request)
	{
		$path = $request->file("profile_photo")->storePublicly("public/{$request->user()->pub_userid}/photos");
		if (!$path) {
			throw new Exception("Couldn't process upload.");
		}

		return Str::replace("public/", "storage/", $path);
	}
}
