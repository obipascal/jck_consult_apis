<?php namespace JCKCon\Http\Handlers\Promos;

use App\Http\Handlers\Core\BaseHandler;
use App\Http\Modules\Modules;
use App\Models\Users\User;
use App\Utilities\PercentageCalculator;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use JCKCon\Enums\APIResponseCodes;
use JCKCon\Enums\APIResponseMessages;

class PromosHandler
{
	use BaseHandler;

	/**
	 * Generate a new promo code
	 *
	 * @return PromosHandler
	 */
	public function createPromoCode(): PromosHandler
	{
		try {
			DB::beginTransaction();

			$params = $this->request->all(["valid_from", "valid_to", "disc_percentage"]);

			/* valid the starting of the promo. It most not be in the pass */
			$now = Carbon::now();
			$start = Carbon::createFromDate($params["valid_from"]);

			if ($start >= $now !== true) {
				return $this->raise(APIResponseMessages::INVALID_START_DATE->value, null, APIResponseCodes::CLIENT_ERR->value);
			}

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

	/**
	 * Update promo code
	 *
	 * @param string $id
	 *
	 * @return PromosHandler
	 */
	public function updatePromoCode(string $id): PromosHandler
	{
		try {
			DB::beginTransaction();

			$params = $this->request->all(["valid_from", "valid_to", "disc_percentage"]);

			/* remove empty values. */
			foreach ($params as $param => $value) {
				if (empty($value)) {
					unset($params[$param]);
				}
			}

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

	/**
	 * Fetch a particular promo code
	 *
	 * @param string $id
	 *
	 * @return PromosHandler
	 */
	public function getPromoCode(string $id): PromosHandler
	{
		try {
			DB::beginTransaction();

			if (!($Promo = Modules::Promo()->get($id))) {
				return $this->raise(APIResponseMessages::NOT_FOUND->value, null, APIResponseCodes::NOT_FOUND->value);
			}

			/* Add additional information about the promotion for more clusure. */
			$from = Carbon::createFromDate($Promo->valid_from);
			$to = Carbon::createFromDate($Promo->valid_to);

			$Promo->starts_on = $from->toDayDateTimeString();
			$Promo->ends_on = $to->toDayDateTimeString();
			$Promo->duration_in_days = $from->diff($to)->days;
			$Promo->discount_text = "{$Promo->disc_percentage}% off";

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

	/**
	 * Fetch promo codes
	 *
	 * @return PromosHandler
	 */
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

	/**
	 * Delete promo code
	 *
	 * @param string $id
	 *
	 * @return PromosHandler
	 */
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

	/**
	 * Apply promo code
	 *
	 * @return PromosHandler
	 */
	public function applycode(): PromosHandler
	{
		try {
			DB::beginTransaction();

			$params = $this->request->all(["code", "course_id"]);

			/** @var User */
			$User = $this->request->user();

			/* get the code  */
			if (!($Code = Modules::Promo()->get($params["code"]))) {
				return $this->raise(APIResponseMessages::DB_ERROR->value, null, APIResponseCodes::SERVER_ERR->value);
			}

			/* get the course  */
			if (!($Course = Modules::Courses()->get($params["course_id"]))) {
				return $this->raise(APIResponseMessages::DB_ERROR->value, null, APIResponseCodes::SERVER_ERR->value);
			}

			$fromDate = Carbon::createFromDate($Code->valid_from);
			$toDate = Carbon::createFromDate($Code->valid_to);
			$todaysDate = Carbon::now();

			/* validate the promo code */
			if ($fromDate > $todaysDate) {
				/* promo is start soon */
				return $this->raise(APIResponseMessages::inActivePromoCode($fromDate->toDayDateTimeString()), null, APIResponseCodes::CLIENT_ERR->value);
			}

			if ($todaysDate > $toDate) {
				/* code has expired */
				return $this->raise(APIResponseMessages::PROMO_EXP->value, null, APIResponseCodes::CLIENT_ERR->value);
			}

			if (!Modules::Promo()->isCodeApplied($Code->promo_id, $User->account_id)) {
				if ($todaysDate >= $fromDate && $toDate >= $todaysDate) {
					$usageData["status"] = "applied";
					$usageData["discounted_amount"] = PercentageCalculator::PercentageOfX($Code->disc_percentage, $Course->price);

					if (!($Usage = Modules::Promo()->addCodeUsage($Code->promo_id, $User->account_id, $usageData))) {
						return $this->raise(APIResponseMessages::DB_ERROR->value, null, APIResponseCodes::SERVER_ERR->value);
					}
				}
			} else {
				$Usage = Modules::Promo()->getCodeUsage($Code->promo_id, $User->account_id);
			}

			//-----------------------------------------------------

			/** Request response data */
			$responseMessage = "Success, promo code applied successfully!";
			$response["type"] = "promo";
			$response["body"] = $Usage;
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
