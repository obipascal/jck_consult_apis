<?php namespace JCKCon\Http\Handlers\Promos;

use App\Http\Handlers\Core\BaseHandler;
use App\Http\Modules\Modules;
use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use JCKCon\Enums\APIResponseCodes;
use JCKCon\Enums\APIResponseMessages;

class PromosHandler
{
	use BaseHandler;

	public function createPromoCode(): PromosHandler
	{
		try {
			DB::beginTransaction();

			$params = $this->request->all(["valid_from", "valid_to", "disc_percentage"]);

			if (!($Promo = Modules::Promo()->add($params))) {
				return $this->raise(APIResponseMessages::DB_ERROR->value, null, APIResponseCodes::SERVER_ERR->value);
			}

			//-----------------------------------------------------

			/** Request response data */
			$responseMessage = "Success, promo code generated successfully!";
			$response["type"] = "promo";
			$response["body"] = $Promo;
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

	public function updatePromoCode(string $id): PromosHandler
	{
		try {
			DB::beginTransaction();

			$params = $this->request->all(["valid_from", "valid_to", "disc_percentage"]);

			if (!Modules::Promo()->update($id, $params)) {
				return $this->raise(APIResponseMessages::DB_ERROR->value, null, APIResponseCodes::SERVER_ERR->value);
			}

			$Promo = Modules::Promo()->get($id);

			//-----------------------------------------------------

			/** Request response data */
			$responseMessage = "Success, promo code updated successfully!";
			$response["type"] = "promo";
			$response["body"] = $Promo;
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

	public function getPromoCode(string $id): PromosHandler
	{
		try {
			DB::beginTransaction();

			if (!($Promo = Modules::Promo()->get($id))) {
				return $this->raise(APIResponseMessages::NOT_FOUND->value, null, APIResponseCodes::NOT_FOUND->value);
			}

			//-----------------------------------------------------

			/** Request response data */
			$responseMessage = "Success, promo code retrieved";
			$response["type"] = "promo";
			$response["body"] = $Promo;
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

	public function getPromoCodes(): PromosHandler
	{
		try {
			DB::beginTransaction();
			$perPage = $this->request->get("perPage") ?? 50;

			if (!($Promos = Modules::Promo()->getPromoCodes($perPage))) {
				return $this->raise(APIResponseMessages::DB_ERROR->value, null, APIResponseCodes::SERVER_ERR->value);
			}

			//-----------------------------------------------------

			/** Request response data */
			$responseMessage = "Success, promo codes retrieved";
			$response["type"] = "promo";
			$response["body"] = $Promos;
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

	public function deletePromoCode(string $id): PromosHandler
	{
		try {
			DB::beginTransaction();

			if (!Modules::Promo()->remove($id)) {
				return $this->raise(APIResponseMessages::DB_ERROR->value, null, APIResponseCodes::SERVER_ERR->value);
			}

			//-----------------------------------------------------

			/** Request response data */
			$responseMessage = "Success, promo code deleted";
			$response["type"] = "promo";
			$response["body"] = null;
			$responseCode = 204;

			DB::commit();

			return $this->response($response, $responseMessage, $responseCode);
		} catch (Exception $th) {
			Log::error($th->getMessage(), ["Line" => $th->getLine(), "file" => $th->getFile()]);

			DB::rollBack();
			DB::commit();

			return $this->raise();
		}
	}

	// ------------------------------------------------------------------
}
