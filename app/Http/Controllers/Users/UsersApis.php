<?php

namespace App\Http\Controllers\Users;

use App\Http\Config\RESTResponse;
use App\Http\Controllers\Controller;
use App\Http\Handlers\Handlers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class UsersApis extends Controller
{
	use RESTResponse;

	/**
	 * Show resources
	 */
	public function index()
	{
		try {
			/* Run validation  */
			$validator = Validator::make(request()->only(["perPage"]), [
				"perPage" => ["bail", "numeric", "nullable"],
			]);

			/* Check if any validation fails */
			if ($validator->fails()) {
				/* If fails return the validation error message  */
				return $this->terminateRequest("Validation Error", $this->getValidationMessages($validator));
			}

			/* Call the controller handlers to handle request logic */
			$handler = Handlers::Users(request())->fetchUsers();

			/* Determine handler operation status  */
			if (!$handler->STATE) {
				/* If operation didn't succeed return the error that was generated by the operation */
				return $this->terminateRequest($handler->ERROR, $handler->RESPONSE, $handler->CODE);
			}

			/* Finally all went well return the response to caller/client */
			return $this->sendResponse($handler->RESPONSE, $handler->MESSAGE, $handler->STATE, $handler->CODE);
		} catch (\Exception $th) {
			Log::error($th->getMessage(), ["file" => $th->getFile(), "line" => $th->getLine()]);

			return $this->terminateRequest("ERROR", $this->RESPONSE ?? $th->getMessage(), 500);
		}
	}

	/**
	 * Show resources
	 */
	public function users()
	{
		try {
			/* Call the controller handlers to handle request logic */
			$handler = Handlers::Users(request())->users();

			/* Determine handler operation status  */
			if (!$handler->STATE) {
				/* If operation didn't succeed return the error that was generated by the operation */
				return $this->terminateRequest($handler->ERROR, $handler->RESPONSE, $handler->CODE);
			}

			/* Finally all went well return the response to caller/client */
			return $this->sendResponse($handler->RESPONSE, $handler->MESSAGE, $handler->STATE, $handler->CODE);
		} catch (\Exception $th) {
			Log::error($th->getMessage(), ["file" => $th->getFile(), "line" => $th->getLine()]);

			return $this->terminateRequest("ERROR", $this->RESPONSE ?? $th->getMessage(), 500);
		}
	}

	/**
	 * Store a newly created resource in storage.
	 */
	public function store(Request $request)
	{
		try {
			/* Run validation  */
			$validator = Validator::make($request->only(["email"]), [
				"email" => ["bail", "email", "required", "unique:users,email"],
			]);

			/* Check if any validation fails */
			if ($validator->fails()) {
				/* If fails return the validation error message  */
				return $this->terminateRequest("Validation Error", $this->getValidationMessages($validator));
			}
			/* Call the controller handlers to handle request logic */
			$handler = Handlers::Users($request)->create();

			/* Determine handler operation status  */
			if (!$handler->STATE) {
				/* If operation didn't succeed return the error that was generated by the operation */
				return $this->terminateRequest($handler->ERROR, $handler->RESPONSE, $handler->CODE);
			}

			/* Finally all went well return the response to caller/client */
			return $this->sendResponse($handler->RESPONSE, $handler->MESSAGE, $handler->STATE, $handler->CODE);
		} catch (\Exception $th) {
			Log::error($th->getMessage(), ["file" => $th->getFile(), "line" => $th->getLine()]);

			return $this->terminateRequest("ERROR", $this->RESPONSE ?? $th->getMessage(), 500);
		}
	}

	/**
	 * Store a newly created resource in storage.
	 */
	public function verify(Request $request)
	{
		try {
			/* Run validation  */
			$validator = Validator::make($request->only(["otp_code"]), [
				"otp_code" => ["bail", "numeric", "required", "exists:o_t_ps,otp_code"],
			]);

			/* Check if any validation fails */
			if ($validator->fails()) {
				/* If fails return the validation error message  */
				return $this->terminateRequest("Validation Error", $this->getValidationMessages($validator));
			}
			/* Call the controller handlers to handle request logic */
			$handler = Handlers::Users($request)->verify();

			/* Determine handler operation status  */
			if (!$handler->STATE) {
				/* If operation didn't succeed return the error that was generated by the operation */
				return $this->terminateRequest($handler->ERROR, $handler->RESPONSE, $handler->CODE);
			}

			/* Finally all went well return the response to caller/client */
			return $this->sendResponse($handler->RESPONSE, $handler->MESSAGE, $handler->STATE, $handler->CODE);
		} catch (\Exception $th) {
			Log::error($th->getMessage(), ["file" => $th->getFile(), "line" => $th->getLine()]);

			return $this->terminateRequest("ERROR", $this->RESPONSE ?? $th->getMessage(), 500);
		}
	}

	/**
	 * Display the specified resource.
	 */
	public function show(string $id)
	{
		try {
			/* Run validation  */
			$validator = Validator::make(
				["account_id" => $id],
				[
					"account_id" => ["bail", "numeric", "required", "exists:users,account_id"],
				]
			);

			/* Check if any validation fails */
			if ($validator->fails()) {
				/* If fails return the validation error message  */
				return $this->terminateRequest("Validation Error", $this->getValidationMessages($validator));
			}
			/* Call the controller handlers to handle request logic */
			$handler = Handlers::Users(request())->fetchProfile($id);

			/* Determine handler operation status  */
			if (!$handler->STATE) {
				/* If operation didn't succeed return the error that was generated by the operation */
				return $this->terminateRequest($handler->ERROR, $handler->RESPONSE, $handler->CODE);
			}

			/* Finally all went well return the response to caller/client */
			return $this->sendResponse($handler->RESPONSE, $handler->MESSAGE, $handler->STATE, $handler->CODE);
		} catch (\Exception $th) {
			Log::error($th->getMessage(), ["file" => $th->getFile(), "line" => $th->getLine()]);

			return $this->terminateRequest("ERROR", $this->RESPONSE ?? $th->getMessage(), 500);
		}
	}

	/**
	 * Update the specified resource in storage.
	 */
	public function update(Request $request, string $id)
	{
		try {
			/* Run validation  */
			$validator = Validator::make(
				["account_id" => $id, ...$request->only(["first_name", "last_name", "phone_number", "gender", "qualification", "password"])],
				[
					"account_id" => ["bail", "numeric", "nullable", "exists:users,account_id"],
					"first_name" => ["bail", "string", "nullable"],
					"last_name" => ["bail", "string", "nullable"],
					"phone_number" => ["bail", "string", "nullable"],
					"gender" => ["bail", "string", "nullable", Rule::in(["male", "female", "others"])],
					"qualification" => ["bail", "string", "nullable", Rule::in(["undergraduate", "graduate", "postgraduate"])],
					"password" => [
						"bail",
						"string",
						"nullable",
						Password::min(8)
							->mixedCase()
							->letters()
							->symbols()
							->numbers(),
					],
				]
			);

			/* Check if any validation fails */
			if ($validator->fails()) {
				/* If fails return the validation error message  */
				return $this->terminateRequest("Validation Error", $this->getValidationMessages($validator));
			}
			/* Call the controller handlers to handle request logic */
			$handler = Handlers::Users(request())->updateProfile($id);

			/* Determine handler operation status  */
			if (!$handler->STATE) {
				/* If operation didn't succeed return the error that was generated by the operation */
				return $this->terminateRequest($handler->ERROR, $handler->RESPONSE, $handler->CODE);
			}

			/* Finally all went well return the response to caller/client */
			return $this->sendResponse($handler->RESPONSE, $handler->MESSAGE, $handler->STATE, $handler->CODE);
		} catch (\Exception $th) {
			Log::error($th->getMessage(), ["file" => $th->getFile(), "line" => $th->getLine()]);

			return $this->terminateRequest("ERROR", $this->RESPONSE ?? $th->getMessage(), 500);
		}
	}

	/**
	 * Update the specified resource in storage.
	 */
	public function updateUser(Request $request)
	{
		try {
			/* Run validation  */
			$validator = Validator::make($request->only(["first_name", "last_name", "phone_number", "gender", "qualification", "password"]), [
				"first_name" => ["bail", "string", "nullable"],
				"last_name" => ["bail", "string", "nullable"],
				"phone_number" => ["bail", "string", "nullable"],
				"gender" => ["bail", "string", "nullable", Rule::in(["male", "female", "others"])],
				"qualification" => ["bail", "string", "nullable", Rule::in(["undergraduate", "graduate", "postgraduate"])],
				"password" => [
					"bail",
					"string",
					"nullable",
					Password::min(8)
						->mixedCase()
						->letters()
						->symbols()
						->numbers(),
				],
			]);

			/* Check if any validation fails */
			if ($validator->fails()) {
				/* If fails return the validation error message  */
				return $this->terminateRequest("Validation Error", $this->getValidationMessages($validator));
			}
			/* Call the controller handlers to handle request logic */
			$handler = Handlers::Users(request())->updateUser();

			/* Determine handler operation status  */
			if (!$handler->STATE) {
				/* If operation didn't succeed return the error that was generated by the operation */
				return $this->terminateRequest($handler->ERROR, $handler->RESPONSE, $handler->CODE);
			}

			/* Finally all went well return the response to caller/client */
			return $this->sendResponse($handler->RESPONSE, $handler->MESSAGE, $handler->STATE, $handler->CODE);
		} catch (\Exception $th) {
			Log::error($th->getMessage(), ["file" => $th->getFile(), "line" => $th->getLine()]);

			return $this->terminateRequest("ERROR", $this->RESPONSE ?? $th->getMessage(), 500);
		}
	}

	/**
	 * Update the specified resource in storage.
	 */
	public function billing(Request $request, string $id)
	{
		try {
			/* Run validation  */
			$validator = Validator::make(
				["account_id" => $id, ...$request->only(["city", "country", "house_no", "street"])],
				[
					"account_id" => ["bail", "numeric", "required", "exists:users,account_id"],
					"city" => ["bail", "string", "required"],
					"country" => ["bail", "string", "required"],
					"house_no" => ["bail", "string", "required"],
					"street" => ["bail", "string", "required"],
				]
			);

			/* Check if any validation fails */
			if ($validator->fails()) {
				/* If fails return the validation error message  */
				return $this->terminateRequest("Validation Error", $this->getValidationMessages($validator));
			}
			/* Call the controller handlers to handle request logic */
			$handler = Handlers::Users(request())->billingInfo($id);

			/* Determine handler operation status  */
			if (!$handler->STATE) {
				/* If operation didn't succeed return the error that was generated by the operation */
				return $this->terminateRequest($handler->ERROR, $handler->RESPONSE, $handler->CODE);
			}

			/* Finally all went well return the response to caller/client */
			return $this->sendResponse($handler->RESPONSE, $handler->MESSAGE, $handler->STATE, $handler->CODE);
		} catch (\Exception $th) {
			Log::error($th->getMessage(), ["file" => $th->getFile(), "line" => $th->getLine()]);

			return $this->terminateRequest("ERROR", $this->RESPONSE ?? $th->getMessage(), 500);
		}
	}

	/**
	 * Remove the specified resource from storage.
	 */
	public function destroy(string $id)
	{
		try {
			/* Run validation  */
			$validator = Validator::make(
				["account_id" => $id],
				[
					"account_id" => ["bail", "numeric", "required", "exists:users,account_id"],
				]
			);

			/* Check if any validation fails */
			if ($validator->fails()) {
				/* If fails return the validation error message  */
				return $this->terminateRequest("Validation Error", $this->getValidationMessages($validator));
			}
			/* Call the controller handlers to handle request logic */
			$handler = Handlers::Users(request())->deleteUser($id);

			/* Determine handler operation status  */
			if (!$handler->STATE) {
				/* If operation didn't succeed return the error that was generated by the operation */
				return $this->terminateRequest($handler->ERROR, $handler->RESPONSE, $handler->CODE);
			}

			/* Finally all went well return the response to caller/client */
			return $this->sendResponse($handler->RESPONSE, $handler->MESSAGE, $handler->STATE, $handler->CODE);
		} catch (\Exception $th) {
			Log::error($th->getMessage(), ["file" => $th->getFile(), "line" => $th->getLine()]);

			return $this->terminateRequest("ERROR", $this->RESPONSE ?? $th->getMessage(), 500);
		}
	}

	// -----------------> [Password]

	/**
	 * Store a newly created resource in storage.
	 */
	public function forgotPassword(Request $request)
	{
		try {
			/* Run validation  */
			$validator = Validator::make($request->only(["email"]), [
				"email" => ["bail", "email", "required", "exists:users,email"],
			]);

			/* Check if any validation fails */
			if ($validator->fails()) {
				/* If fails return the validation error message  */
				return $this->terminateRequest("Validation Error", $this->getValidationMessages($validator));
			}
			/* Call the controller handlers to handle request logic */
			$handler = Handlers::Users($request)->forgotPassword();

			/* Determine handler operation status  */
			if (!$handler->STATE) {
				/* If operation didn't succeed return the error that was generated by the operation */
				return $this->terminateRequest($handler->ERROR, $handler->RESPONSE, $handler->CODE);
			}

			/* Finally all went well return the response to caller/client */
			return $this->sendResponse($handler->RESPONSE, $handler->MESSAGE, $handler->STATE, $handler->CODE);
		} catch (\Exception $th) {
			Log::error($th->getMessage(), ["file" => $th->getFile(), "line" => $th->getLine()]);

			return $this->terminateRequest("ERROR", $this->RESPONSE ?? $th->getMessage(), 500);
		}
	}

	/**
	 * Store a newly created resource in storage.
	 */
	public function forgotPassword_confirmReset(Request $request)
	{
		try {
			/* Run validation  */
			$validator = Validator::make($request->only(["otp_code"]), [
				"otp_code" => ["bail", "numeric", "required", "exists:o_t_ps,otp_code"],
			]);

			/* Check if any validation fails */
			if ($validator->fails()) {
				/* If fails return the validation error message  */
				return $this->terminateRequest("Validation Error", $this->getValidationMessages($validator));
			}
			/* Call the controller handlers to handle request logic */
			$handler = Handlers::Users($request)->confirmPasswordReset();

			/* Determine handler operation status  */
			if (!$handler->STATE) {
				/* If operation didn't succeed return the error that was generated by the operation */
				return $this->terminateRequest($handler->ERROR, $handler->RESPONSE, $handler->CODE);
			}

			/* Finally all went well return the response to caller/client */
			return $this->sendResponse($handler->RESPONSE, $handler->MESSAGE, $handler->STATE, $handler->CODE);
		} catch (\Exception $th) {
			Log::error($th->getMessage(), ["file" => $th->getFile(), "line" => $th->getLine()]);

			return $this->terminateRequest("ERROR", $this->RESPONSE ?? $th->getMessage(), 500);
		}
	}

	/** Store a newly created resource in storage.
	 */
	public function forgotPassword_resetPassword(Request $request)
	{
		try {
			/* Run validation  */
			$validator = Validator::make($request->only(["reset_token", "password"]), [
				"reset_token" => ["bail", "string", "required"],
				"password" => [
					"bail",
					"string",
					"required",
					Password::min(8)
						->mixedCase()
						->letters()
						->symbols()
						->numbers(),
				],
			]);

			/* Check if any validation fails */
			if ($validator->fails()) {
				/* If fails return the validation error message  */
				return $this->terminateRequest("Validation Error", $this->getValidationMessages($validator));
			}
			/* Call the controller handlers to handle request logic */
			$handler = Handlers::Users($request)->resetPassword();

			/* Determine handler operation status  */
			if (!$handler->STATE) {
				/* If operation didn't succeed return the error that was generated by the operation */
				return $this->terminateRequest($handler->ERROR, $handler->RESPONSE, $handler->CODE);
			}

			/* Finally all went well return the response to caller/client */
			return $this->sendResponse($handler->RESPONSE, $handler->MESSAGE, $handler->STATE, $handler->CODE);
		} catch (\Exception $th) {
			Log::error($th->getMessage(), ["file" => $th->getFile(), "line" => $th->getLine()]);

			return $this->terminateRequest("ERROR", $this->RESPONSE ?? $th->getMessage(), 500);
		}
	}

	// ---------------------------------------------> [Authentication]
	/**
	 * Display a listing of the resource.
	 */
	public function authorizedAccountLogin(Request $request)
	{
		try {
			/* Run validation  */
			$validator = Validator::make($request->only(["username", "password"]), [
				"username" => ["bail", "email", "required", "exists:users,email"],
				"password" => ["bail", "string", "required"],
			]);

			/* Check if any validation fails */
			if ($validator->fails()) {
				/* If fails return the validation error message  */
				return $this->terminateRequest("Validation Error", $this->getValidationMessages($validator));
			}
			/* Call the controller handlers to handle request logic */
			$handler = Handlers::Users($request)->authorized();

			/* Determine handler operation status  */
			if (!$handler->STATE) {
				/* If operation didn't succeed return the error that was generated by the operation */
				return $this->terminateRequest($handler->ERROR, $handler->RESPONSE, $handler->CODE);
			}

			/* Finally all went well return the response to caller/client */
			return $this->sendResponse($handler->RESPONSE, $handler->MESSAGE, $handler->STATE, $handler->CODE);
		} catch (\Exception $th) {
			Log::error($th->getMessage(), ["file" => $th->getFile(), "line" => $th->getLine()]);

			return $this->terminateRequest("ERROR", $this->RESPONSE ?? $th->getMessage(), 500);
		}
	}

	/**
	 * Display a listing of the resource.
	 */
	public function user(Request $request)
	{
		try {
			/* Call the controller handlers to handle request logic */
			$handler = Handlers::Users($request)->fetchUser();

			/* Determine handler operation status  */
			if (!$handler->STATE) {
				/* If operation didn't succeed return the error that was generated by the operation */
				return $this->terminateRequest($handler->ERROR, $handler->RESPONSE, $handler->CODE);
			}

			/* Finally all went well return the response to caller/client */
			return $this->sendResponse($handler->RESPONSE, $handler->MESSAGE, $handler->STATE, $handler->CODE);
		} catch (\Exception $th) {
			Log::error($th->getMessage(), ["file" => $th->getFile(), "line" => $th->getLine()]);

			return $this->terminateRequest("ERROR", $this->RESPONSE ?? $th->getMessage(), 500);
		}
	}
}
