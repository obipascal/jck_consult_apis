<?php namespace JCKCon\Http\Handlers\Settings;

use App\Http\Handlers\Core\BaseHandler;
use App\Http\Modules\Modules;
use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use JCKCon\Enums\APIResponseCodes;
use JCKCon\Enums\APIResponseMessages;

class SettingsHandler
{
	use BaseHandler;

	protected function processSiteLogoUpload(): bool|string
	{
		try {
			DB::beginTransaction();

			$siteLogo = $this->request->file("site_logo");

			if (!$siteLogo->isValid()) {
				return false;
			}

			$path = $siteLogo->storePublicly("public/site_settings/logos");

			return Str::replace("public/", "storage/", $path);
			//-----------------------------------------------------

			DB::commit();
		} catch (Exception $th) {
			Log::error($th->getMessage(), ["Line" => $th->getLine(), "file" => $th->getFile()]);

			DB::rollBack();
			DB::commit();

			return false;
		}
	}

	public function saveSettings(): SettingsHandler
	{
		try {
			DB::beginTransaction();

			$params = $this->request->all(["name", "desc", "about", "email", "phone_number", "line_address", "facebook_handle", "twitter_handle", "whatsapp_handle", "instagram_handle", "linkedin_handle"]);

			if (!($Settings = Modules::Settings()->add($params))) {
				DB::rollBack();
				DB::commit();

				return $this->raise(APIResponseMessages::DB_ERROR->value, null, APIResponseCodes::SERVER_ERR->value);
			}

			/* process image upload */
			if (!empty($this->request->file("site_logo"))) {
				if (!($logoPath = $this->processSiteLogoUpload())) {
					return $this->raise(APIResponseMessages::UPL_ERR->value, null, APIResponseCodes::SERVER_ERR->value);
				}

				if (!Modules::Settings()->update($Settings->site_id, ["logo" => $logoPath])) {
					DB::rollBack();
					DB::commit();

					return $this->raise(APIResponseMessages::DB_ERROR->value, null, APIResponseCodes::SERVER_ERR->value);
				}
			}

			/* fetch updated settings values from storage. */
			$Settings = Modules::Settings()->get($Settings->site_id);

			//-----------------------------------------------------

			/** Request response data */
			$responseMessage = "Success, application settings saved successfully!";
			$response["type"] = "settings";
			$response["body"] = $Settings;
			$responseCode = Modules::Settings()->hasSettings() ? 200 : 201;

			DB::commit();

			return $this->response($response, $responseMessage, $responseCode);
		} catch (Exception $th) {
			Log::error($th->getMessage(), ["Line" => $th->getLine(), "file" => $th->getFile()]);

			DB::rollBack();
			DB::commit();

			return $this->raise();
		}
	}

	public function getSettings(): SettingsHandler
	{
		try {
			DB::beginTransaction();

			if (!($Settings = Modules::Settings()->getConfigs())) {
				DB::rollBack();
				DB::commit();

				return $this->raise(APIResponseMessages::DB_ERROR->value, null, APIResponseCodes::SERVER_ERR->value);
			}

			//-----------------------------------------------------

			/** Request response data */
			$responseMessage = "Success, application settings retreived";
			$response["type"] = "settings";
			$response["body"] = $Settings;
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

	public function getSetting(string $id)
	{
		try {
			DB::beginTransaction();

			//-----------------------------------------------------

			/** Request response data */
			$responseMessage = "Success, settings retrieved";
			$response["type"] = "settings";
			$response["body"] = Modules::Settings()->get($id);
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

	public function createFQA(): SettingsHandler
	{
		try {
			DB::beginTransaction();

			$params = $this->request->all(["title", "content"]);

			if (!($FAQ = Modules::Settings()->addFAQ($params))) {
				DB::rollBack();
				DB::commit();

				return $this->raise(APIResponseMessages::DB_ERROR->value, null, APIResponseCodes::SERVER_ERR->value);
			}

			//-----------------------------------------------------

			/** Request response data */
			$responseMessage = "Success, FAQ added successfully!";
			$response["type"] = "settings";
			$response["body"] = $FAQ;
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

	public function updateFAQ(string $id): SettingsHandler
	{
		try {
			DB::beginTransaction();

			$params = $this->request->all(["title", "content"]);

			if (!Modules::Settings()->updateFAQ($id, $params)) {
				DB::rollBack();
				DB::commit();

				return $this->raise(APIResponseMessages::DB_ERROR->value, null, APIResponseCodes::SERVER_ERR->value);
			}

			$FAQ = Modules::Settings()->getFAQ($id);

			//-----------------------------------------------------

			/** Request response data */
			$responseMessage = "Success, FAQ updated successfully!";
			$response["type"] = "settings";
			$response["body"] = $FAQ;
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
	public function getFAQ(string $id): SettingsHandler
	{
		try {
			DB::beginTransaction();

			if (!($FAQ = Modules::Settings()->getFAQ($id))) {
				DB::rollBack();
				DB::commit();

				return $this->raise(APIResponseMessages::DB_ERROR->value, null, APIResponseCodes::SERVER_ERR->value);
			}

			//-----------------------------------------------------

			/** Request response data */
			$responseMessage = "Success, FAQ retrieved";
			$response["type"] = "settings";
			$response["body"] = $FAQ;
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

	public function getFAQs()
	{
		try {
			DB::beginTransaction();

			$perPage = $this->request->get("perPage") ?? 100;

			//-----------------------------------------------------

			/** Request response data */
			$responseMessage = "Success, system faqs retrieved";
			$response["type"] = "settings";
			$response["body"] = Modules::Settings()->getAllFAQ($perPage);
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
	public function deleteFAQ(string $id): SettingsHandler
	{
		try {
			DB::beginTransaction();

			if (!Modules::Settings()->removeFAQ($id)) {
				DB::rollBack();
				DB::commit();

				return $this->raise(APIResponseMessages::DB_ERROR->value, null, APIResponseCodes::SERVER_ERR->value);
			}

			//-----------------------------------------------------

			/** Request response data */
			$responseMessage = "Success, FAQ Deleted successfully!";
			$response["type"] = "settings";
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
}