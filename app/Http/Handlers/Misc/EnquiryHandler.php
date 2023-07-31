<?php namespace JCKCon\Http\Handlers\Misc;

use App\Http\Modules\Modules;
use App\Mail\CustomerEnquiry;
use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use JCKCon\Enums\APIResponseCodes;
use JCKCon\Enums\APIResponseMessages;

trait EnquiryHandler
{
	public function makeEnquiry($params = null): MiscHandler
	{
		try {
			DB::beginTransaction();

			$params = $this->request->all(["course_id", "subject", "first_name", "last_name", "email", "phone_number", "message"]);

			if (!($Enquiry = Modules::Misc()->saveEnquiry($params))) {
				return $this->raise(APIResponseMessages::DB_ERROR->value, null, APIResponseCodes::SERVER_ERR->value);
			}

			$Configs = Modules::Settings()->getConfigs();
			$siteName = $Configs->name;

			Mail::to($Enquiry)->send(new CustomerEnquiry("{$Enquiry->first_name} {$Enquiry->last_name}", $Enquiry->email, $Enquiry->phone_number, $Enquiry->message, $Enquiry->subject));

			//-----------------------------------------------------

			/** Request response data */
			$responseMessage = "Success, Thank you for reaching out to {$siteName}, you messaged has been well recieved and we will be in touch shortly.";
			$response["type"] = "misc";
			$response["body"] = $Enquiry;
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

	public function getEnquiry(string $id): MiscHandler
	{
		try {
			DB::beginTransaction();

			if (!($Enquiry = Modules::Misc()->getEnquiry($id))) {
				return $this->raise(APIResponseMessages::DB_ERROR->value, null, APIResponseCodes::SERVER_ERR->value);
			}

			//-----------------------------------------------------

			/** Request response data */
			$responseMessage = "Success, enquiry retrieved";
			$response["type"] = "misc";
			$response["body"] = $Enquiry;
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

	public function getEnquiries(): MiscHandler
	{
		try {
			DB::beginTransaction();

			$perPage = $this->request->get("perPage") ?? 50;

			if (!($EQ = Modules::Misc()->getEnquiries($perPage))) {
				return $this->raise(APIResponseMessages::DB_ERROR->value, null, APIResponseCodes::SERVER_ERR->value);
			}

			//-----------------------------------------------------

			/** Request response data */
			$responseMessage = "Success, enquiries retrieved";
			$response["type"] = "misc";
			$response["body"] = $EQ;
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

	public function deleteEnquiry(string $id): MiscHandler
	{
		try {
			DB::beginTransaction();

			if (!Modules::Misc()->removeEnquiry($id)) {
				return $this->raise(APIResponseMessages::DB_ERROR->value, null, APIResponseCodes::SERVER_ERR->value);
			}

			//-----------------------------------------------------

			/** Request response data */
			$responseMessage = "Success, enquiry retrieved";
			$response["type"] = "misc";
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
}
