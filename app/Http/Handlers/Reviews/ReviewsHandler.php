<?php namespace JCKCon\Http\Handlers\Reviews;

use App\Http\Handlers\Core\BaseHandler;
use App\Http\Modules\Modules;
use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use JCKCon\Enums\APIResponseCodes;
use JCKCon\Enums\APIResponseMessages;

class ReviewsHandler
{
	use BaseHandler;

	protected function processImageUpload(?string $id = null): bool|string
	{
		try {
			$reviewerImage = $this->request->file("reviewer_image");

			if (!$reviewerImage->isValid()) {
				return false;
			}

			if (!empty($id)) {
				/* check if there's an existing site logo already */
				$Review = Modules::Reviews()->get($id);
				if (!empty($Review->reviewer_image)) {
					$path = Str::replace(config("app.url") . "/", "", $Review->reviewer_image);
					$path = Str::replace("storage", "public", $path);

					if (Storage::exists($path)) {
						Storage::delete($path);
					}
				}
			}

			$path = $reviewerImage->storePublicly("public/reviews");

			return Str::replace("public/", "storage/", $path);
			//-----------------------------------------------------
		} catch (Exception $th) {
			Log::error($th->getMessage(), ["Line" => $th->getLine(), "file" => $th->getFile()]);

			return false;
		}
	}

	public function createReview()
	{
		try {
			DB::beginTransaction();

			$params = $this->request->all(["reviewer_name", "reviewer_email", "reviewer_role", "reviewer_company", "reviewer_message"]);

			if (!($reviewerImage = $this->processImageUpload($params["reviewer_email"]))) {
				return $this->raise(APIResponseMessages::UPL_ERR->value, null, APIResponseCodes::CLIENT_ERR->value);
			}

			$params["reviewer_image"] = $reviewerImage;

			if (!($Review = Modules::Reviews()->add($params))) {
				return $this->raise(APIResponseMessages::DB_ERROR->value, null, APIResponseCodes::SERVER_ERR->value);
			}
			//-----------------------------------------------------

			/** Request response data */
			$responseMessage =
				"Success, Your review has been warmly received. Thank you for taking the time to share your thoughts with us! Your feedback is highly valued and will undoubtedly contribute to our ongoing success.";
			$response["type"] = "review";
			$response["body"] = $Review;
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

	public function updateReview(string $id)
	{
		try {
			DB::beginTransaction();

			$params = $this->request->all(["status", "reviewer_name", "reviewer_email", "reviewer_role", "reviewer_company", "reviewer_message"]);

			foreach ($params as $param => $value) {
				if (empty($value)) {
					unset($params[$param]);
				}
			}

			if (!Modules::Reviews()->update($id, $params)) {
				return $this->raise(APIResponseMessages::DB_ERROR->value, null, APIResponseCodes::SERVER_ERR->value);
			}

			$Review = Modules::Reviews()->get($id);
			//-----------------------------------------------------

			/** Request response data */
			$responseMessage = "Success, review updated";
			$response["type"] = "review";
			$response["body"] = $Review;
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

	public function getReview(string $id)
	{
		try {
			DB::beginTransaction();

			$Review = Modules::Reviews()->get($id);
			//-----------------------------------------------------

			/** Request response data */
			$responseMessage = "Success, review retreived";
			$response["type"] = "review";
			$response["body"] = $Review;
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

	public function getReviews()
	{
		try {
			DB::beginTransaction();

			$perPage = $this->request->get("perPage") ?? 50;

			$Reviews = Modules::Reviews()->all($perPage);
			//-----------------------------------------------------

			/** Request response data */
			$responseMessage = "Success, reviews retreived";
			$response["type"] = "review";
			$response["body"] = $Reviews;
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

	public function getPublishedReviews()
	{
		try {
			DB::beginTransaction();

			$perPage = $this->request->get("perPage") ?? 50;

			$Reviews = Modules::Reviews()->published($perPage);
			//-----------------------------------------------------

			/** Request response data */
			$responseMessage = "Success, reviews retreived";
			$response["type"] = "review";
			$response["body"] = $Reviews;
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

	public function deleteReview(string $id)
	{
		try {
			DB::beginTransaction();

			if (!Modules::Reviews()->remove($id)) {
				return $this->raise(APIResponseMessages::DB_ERROR->value, null, APIResponseCodes::SERVER_ERR->value);
			}
			//-----------------------------------------------------

			/** Request response data */
			$responseMessage = "Success, review deleted";
			$response["type"] = "review";
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
