<?php namespace JCKCon\Http\Handlers\Courses;

use App\Http\Modules\Modules;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use JCKCon\Enums\APIResponseCodes;
use JCKCon\Enums\APIResponseMessages;

use function App\Utilities\formatFileSizeUnits;

trait CourseMaterialHandler
{
	protected function processUpload(?string $id = null): bool|array
	{
		try {
			/** @var Request */
			$request = $this->request;

			$courseMaterial = $request->file("upload_file");

			if (!$courseMaterial->isValid()) {
				return false;
			}

			if (!empty($id)) {
				/* check if there's an existing site logo already */
				$Material = Modules::Courses()->getMaterial($id);
				if (!empty($Material->file)) {
					$path = Str::replace(config("app.url") . "/", "", $Material->file);
					$path = Str::replace("storage", "public", $path);

					if (Storage::exists($path)) {
						Storage::delete($path);
					}
				}
			}

			$path = $courseMaterial->storePublicly("public/courses");

			$param["file"] = Str::replace("public/", "storage/", $path);
			$param["type"] = $courseMaterial->getClientOriginalExtension();
			$param["size"] = formatFileSizeUnits($courseMaterial->getSize());

			return $param;
		} catch (Exception $th) {
			Log::error($th->getMessage(), ["Line" => $th->getLine(), "file" => $th->getFile()]);

			return false;
		}
	}

	// -----------------------------------> [Main ]

	public function uploadCourseMaterial()
	{
		try {
			DB::beginTransaction();

			if (!($params = $this->processUpload())) {
				return $this->raise(APIResponseMessages::UPL_ERR->value, null, APIResponseCodes::CLIENT_ERR->value);
			}

			$params["title"] = $this->request->post("title");
			$params["course_id"] = $this->request->post("course");

			if (!($Material = Modules::Courses()->addMaterial($params))) {
				return $this->raise(APIResponseMessages::DB_ERROR->value, null, APIResponseCodes::SERVER_ERR->value);
			}

			//-----------------------------------------------------

			/** Request response data */
			$responseMessage = "Success, course material uploaded.";
			$response["type"] = "courses";
			$response["body"] = $Material;
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

	public function updateCourseMaterial(string $id)
	{
		try {
			DB::beginTransaction();

			$params = $this->request->all(["title"]);

			foreach ($params as $param => $value) {
				if (empty($value)) {
					unset($params[$param]);
				}
			}

			if (!Modules::Courses()->updateMaterial($id, $params)) {
				return $this->raise(APIResponseMessages::DB_ERROR->value, APIResponseCodes::SERVER_ERR->value);
			}

			$Material = Modules::Courses()->getMaterial($id);

			//-----------------------------------------------------

			/** Request response data */
			$responseMessage = "Success, course material updated.";
			$response["type"] = "courses";
			$response["body"] = $Material;
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

	public function updateCourseMaterialFile(string $id)
	{
		try {
			DB::beginTransaction();

			if (!empty($this->request->post("upload_file"))) {
				if (!($params = $this->processUpload($id))) {
					return $this->raise(APIResponseMessages::UPL_ERR->value, null, APIResponseCodes::CLIENT_ERR->value);
				}

				if (!Modules::Courses()->updateMaterial($id, $params)) {
					return $this->raise(APIResponseMessages::DB_ERROR->value, null, APIResponseCodes::SERVER_ERR->value);
				}
			}

			$Material = Modules::Courses()->getMaterial($id);

			//-----------------------------------------------------

			/** Request response data */
			$responseMessage = "Success, course material file updated.";
			$response["type"] = "courses";
			$response["body"] = $Material;
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

	public function getCourseMaterial(string $id)
	{
		try {
			DB::beginTransaction();

			if (!($Material = Modules::Courses()->getMaterial($id))) {
				return $this->raise(APIResponseMessages::DB_ERROR->value, null, APIResponseCodes::SERVER_ERR->value);
			}

			//-----------------------------------------------------

			/** Request response data */
			$responseMessage = "Success, course material retrieved.";
			$response["type"] = "courses";
			$response["body"] = $Material;
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

	public function getCourseMaterials()
	{
		try {
			DB::beginTransaction();

			$perPage = $this->request->get("perPage") ?? 50;

			if (!($Material = Modules::Courses()->getMaterials($perPage))) {
				return $this->raise(APIResponseMessages::UPL_ERR->value, null, APIResponseCodes::CLIENT_ERR->value);
			}

			//-----------------------------------------------------

			/** Request response data */
			$responseMessage = "Success, course materiald retrieved.";
			$response["type"] = "courses";
			$response["body"] = $Material;
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

	public function deleteCourseMaterial(string $id)
	{
		try {
			DB::beginTransaction();

			if (!Modules::Courses()->removeMaterial($id)) {
				return $this->raise(APIResponseMessages::DB_ERROR->value, null, APIResponseCodes::SERVER_ERR->value);
			}

			//-----------------------------------------------------

			/** Request response data */
			$responseMessage = "Success, course material deleted.";
			$response["type"] = "courses";
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
