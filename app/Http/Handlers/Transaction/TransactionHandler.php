<?php namespace JCKCon\Http\Handlers\Transaction;

use App\Http\Handlers\Core\BaseHandler;
use App\Http\Modules\Modules;
use App\Models\Users\User;
use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use JCKCon\Enums\APIResponseCodes;
use JCKCon\Enums\APIResponseMessages;
use JCKCon\Enums\TransStatus;
use Stripe\StripeClient;

use function App\Utilities\random_string;

class TransactionHandler
{
	use BaseHandler;


	public function checkout():TransactionHandler
	{
		try {
			DB::beginTransaction();

			$StripeService = new StripeClient(config("stripe.secret_key"));

			/** @var User */
			$User = $this->request->user();

			$params = $this->request->all(["course", "promo_id"]);

			/* make sure user is not enrolled in the course  */
			if (Modules::Courses()->isEnrolled($User->account_id, $params["course"])) {
				return $this->raise(APIResponseMessages::ALREADY_ENROLLED->value, null, APIResponseCodes::CLIENT_ERR->value);
			}

			/* obtain the course  */
			if (!($Course = Modules::Courses()->get($params["course"]))) {
				return $this->raise(APIResponseMessages::DB_ERROR->value, null, APIResponseCodes::SERVER_ERR->value);
			}

			/* get application config */
			if (!($Configs = Modules::Settings()->getConfigs())) {
				return $this->raise(APIResponseMessages::DB_ERROR->value, null, APIResponseCodes::SERVER_ERR->value);
			}

			/* get the promo if present and apply the discount amount */
			if (!empty($params["promo_id"])) {
				if (!($Promo = Modules::Promo()->getCodeUsage($params["promo_id"], $User->account_id))) {
					return $this->raise("Invalid promo code or promo has expired.");
				}

				if (!$Promo->discounted_amount > $Course->price) {
					return $this->raise("Invalid promo discount amount. Amount cannot be greater than course price.");
				}

				// apply discount to course price
				$Amount = $Course->price - $Promo->discounted_amount;
				$Discount = $Promo->discounted_amount;

				// update the promo status
				if (!Modules::Promo()->updateCodeUsage($Promo->promo_id, $User->account_id, ["status" => "used"])) {
					return $this->raise(APIResponseMessages::DB_ERROR->value, null, APIResponseCodes::SERVER_ERR->value);
				}
			} else {
				$Amount = $Course->price;
				$Discount = 0;
			}

			/* generate a payment  */
			$paymentInt = $StripeService->paymentIntents->create([
				"amount" => $Amount * 100,
				"currency" => config("stripe.currency"),
				"description" => "Course enrollment for {$Course->title}.",
				"receipt_email" => $User->email,
				"statement_descriptor" => $Configs->name,
				"automatic_payment_methods" => [
					"enabled" => true,
				],
			]);

			if (!($_response = json_decode($paymentInt->toJSON()))) {
				return $this->raise(APIResponseMessages::STRIPE_ERROR->value, null, APIResponseCodes::SERVER_ERR->value);
			}

			$_transData["amount"] = $Amount;
			$_transData["discount"] = $Discount;
			$_transData["pi_id"] = $_response->id;
			$_transData["cs_code"] = $_response->client_secret;
			$_transData["course_id"] = $Course->course_id;
			$_transData["account_id"] = $User->account_id;
			$_transData["reference"] = "JCKRF_" . random_string("numeric");

			if (!($Trans = Modules::Trans()->add($_transData))) {
				return $this->raise(APIResponseMessages::DB_ERROR->value, null, APIResponseCodes::SERVER_ERR->value);
			}

			//-----------------------------------------------------

			/** Request response data */
			$responseMessage = "Success, checkout payment initiated";
			$response["type"] = "transactions";
			$response["body"] = $Trans;
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

	public function succeeded(\Stripe\PaymentIntent $paymentInt)
	{
		try {
			DB::beginTransaction();

			if (!($Trans = Modules::Trans()->getFromPayIntent($paymentInt->id))) {
				throw new Exception(APIResponseMessages::NOT_FOUND->value, APIResponseCodes::NOT_FOUND->value);
			}

			/* update transactions status */
			$params["status"] = TransStatus::SUCCESS->value;
			if (!Modules::Trans()->update($Trans->trans_id, $params)) {
				throw new Exception(APIResponseMessages::DB_ERROR->value, APIResponseCodes::SERVER_ERR->value);
			}

			/* get the updated transaction object */
			$Trans = Modules::Trans()->get($Trans->trans_id);

			/**
			 * @todo Add customer to course enrollments and set the status to enrolled
			 */
			$_enrollData["trans_id"] = $Trans->trans_id;
			$_enrollData["account_id"] = $Trans->account_id;
			$_enrollData["course_id"] = $Trans->course_id;
			if (!Modules::Courses()->addEnrollment($_enrollData)) {
				throw new Exception(APIResponseMessages::DB_ERROR->value, APIResponseCodes::SERVER_ERR->value);
			}

			/**
			 * @todo Send customer a payment receipt via email
			 */

			//-----------------------------------------------------

			/** Request response data */
			$responseMessage = "Success";
			$response["type"] = "transaction";
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

	public function transaction(string $id)
	{
		try {
			DB::beginTransaction();

			if (!($Tran = Modules::Trans()->get($id))) {
				return $this->raise(APIResponseMessages::DB_ERROR->value, null, APIResponseCodes::SERVER_ERR->value);
			}

			//-----------------------------------------------------

			/** Request response data */
			$responseMessage = "Success, transaction retrieved";
			$response["type"] = "transaction";
			$response["body"] = $Tran;
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

	public function userTransactions()
	{
		try {
			DB::beginTransaction();

			$perPage = $this->request->get("perPage") ?? 50;
			/** @var User */
			$User = $this->request->user();

			if (!($Trans = Modules::Trans()->usersTransactions($User->account_id, $perPage))) {
				return $this->raise(APIResponseMessages::DB_ERROR->value, null, APIResponseCodes::SERVER_ERR->value);
			}

			//-----------------------------------------------------

			/** Request response data */
			$responseMessage = "Success, user transactions retrieved";
			$response["type"] = "transaction";
			$response["body"] = $Trans;
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

	public function transactions()
	{
		try {
			DB::beginTransaction();

			$perPage = $this->request->get("perPage") ?? 50;

			if (!($Trans = Modules::Trans()->all($perPage))) {
				return $this->raise(APIResponseMessages::DB_ERROR->value, null, APIResponseCodes::SERVER_ERR->value);
			}

			//-----------------------------------------------------

			/** Request response data */
			$responseMessage = "Success, transactions retrieved.";
			$response["type"] = "transactions";
			$response["body"] = $Trans;
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
