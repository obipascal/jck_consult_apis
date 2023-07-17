<?php namespace JCKCon\Http\Handlers\Courses;

use App\Http\Handlers\Core\BaseHandler;
use App\Http\Modules\Modules;
use App\Models\Users\User;
use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use JCKCon\Enums\APIResponseCodes;
use JCKCon\Enums\APIResponseMessages;

class CoursesHandler
{
	use BaseHandler;

	protected function processImageUpload(?string $id = null): bool|string
	{
		try {
			$siteLogo = $this->request->file("image");

			if (!$siteLogo->isValid()) {
				return false;
			}

			if (!empty($id)) {
				/* check if there's an existing site logo already */
				$Course = Modules::Courses()->get($id);
				if (!empty($Course->image)) {
					$path = Str::replace(config("app.url") . "/", "", $Course->image);
					$path = Str::replace("storage", "public", $path);

					if (Storage::exists($path)) {
						Storage::delete($path);
					}
				}
			}

			$path = $siteLogo->storePublicly("public/courses");

			return Str::replace("public/", "storage/", $path);
			//-----------------------------------------------------
		} catch (Exception $th) {
			Log::error($th->getMessage(), ["Line" => $th->getLine(), "file" => $th->getFile()]);

			return false;
		}
	}

	/**
	 * Create a new course
	 *
	 * @return CoursesHandler
	 */
	public function createCourse(): CoursesHandler
	{
		try {
			DB::beginTransaction();

			/** @var User */
			$User = $this->request->user();

			$params = $this->request->all(["title", "desc", "price", "body", "status"]);

			/* Process the file upload first  */
			if (!($path = $this->processImageUpload())) {
				return $this->raise(APIResponseMessages::UPL_ERR->value, null, APIResponseCodes::TECHNICAL_ERR->value);
			}

			/* add the user factor and image to the params */
			$params["account_id"] = $User->account_id;
			$params["image"] = $path;

			if (!($Course = Modules::Courses()->add($params))) {
				DB::rollBack();
				DB::commit();

				return $this->raise(APIResponseMessages::DB_ERROR->value, null, APIResponseCodes::SERVER_ERR->value);
			}

			// get updated course info
			$Course = Modules::Courses()->get($Course->course_id);
			//-----------------------------------------------------

			/** Request response data */
			$responseMessage = "Success, course created successfully!";
			$response["type"] = "courses";
			$response["body"] = $Course;
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
	 * Update course content.
	 *
	 * @param string $id
	 *
	 * @return CoursesHandler
	 */
	public function updateCourse(string $id): CoursesHandler
	{
		try {
			DB::beginTransaction();

			$params = $this->request->all(["title", "desc", "price", "body", "status"]);

			if (!($Course = Modules::Courses()->get($id))) {
				return $this->raise(APIResponseMessages::DB_ERROR->value, null, APIResponseCodes::SERVER_ERR->value);
			}

			/* remove all values that are not present in the params. */
			foreach ($params as $param => $value) {
				if (empty($value)) {
					unset($params[$param]);
				}
			}

			/* update the logo path */
			if (!Modules::Courses()->update($Course->course_id, $params)) {
				DB::rollBack();
				DB::commit();

				return $this->raise(APIResponseMessages::DB_ERROR->value, null, APIResponseCodes::SERVER_ERR->value);
			}

			// get updated course info
			$Course = Modules::Courses()->get($Course->course_id);

			//-----------------------------------------------------

			/** Request response data */
			$responseMessage = "Success, course updated successfully!";
			$response["type"] = "courses";
			$response["body"] = $Course;
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
	 * Update coures image
	 *
	 * @param string $id
	 *
	 * @return CoursesHandler
	 */
	public function updateCourseImage(string $id): CoursesHandler
	{
		try {
			DB::beginTransaction();

			if (!($path = $this->processImageUpload($id))) {
				return $this->raise(APIResponseMessages::UPL_ERR->value, null, APIResponseCodes::TECHNICAL_ERR->value);
			}

			if (!Modules::Courses()->update($id, ["image" => $path])) {
				return $this->raise(APIResponseMessages::DB_ERROR->vaue, null, APIResponseCodes::SERVER_ERR->value);
			}

			$Course = Modules::Courses()->get($id);

			//-----------------------------------------------------

			/** Request response data */
			$responseMessage = "Success, course image updated!";
			$response["type"] = "courses";
			$response["body"] = $Course;
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
	 * Retrieve an individual course by id
	 *
	 * @param string $id
	 *
	 * @return CoursesHandler
	 */
	public function getCourse(string $id): CoursesHandler
	{
		try {
			DB::beginTransaction();

			if (!($Course = Modules::Courses()->get($id))) {
				return $this->raise(APIResponseMessages::DB_ERROR->value, null, APIResponseCodes::SERVER_ERR->value);
			}

			//-----------------------------------------------------

			/** Request response data */
			$responseMessage = "Success, course retreived successfully!";
			$response["type"] = "courses";
			$response["body"] = $Course;
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
	 * Get all published courses
	 *
	 * @return CoursesHandler
	 */
	public function getPubCourses(): CoursesHandler
	{
		try {
			DB::beginTransaction();

			$perPage = $this->request->get("perPage") ?? 100;

			if (!($Courses = Modules::Courses()->getPublishedCourses($perPage))) {
				return $this->raise(APIResponseMessages::DB_ERROR->value, null, APIResponseCodes::SERVER_ERR->value);
			}

			//-----------------------------------------------------

			/** Request response data */
			$responseMessage = "Success, courses retreived successfully!";
			$response["type"] = "courses";
			$response["body"] = $Courses;
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
	 * Get all system courses
	 * @param string $filter This is the status of the course you which to show.
	 * possible values are: published or drafted
	 *
	 * @return CoursesHandler
	 */
	public function getCourses(): CoursesHandler
	{
		try {
			DB::beginTransaction();

			$perPage = $this->request->get("perPage") ?? 100;
			$status = $this->request->get("filter") ?? "all";

			if (!($Courses = Modules::Courses()->getCourses($status, $perPage))) {
				return $this->raise(APIResponseMessages::DB_ERROR->value, null, APIResponseCodes::SERVER_ERR->value);
			}

			//-----------------------------------------------------

			/** Request response data */
			$responseMessage = "Success, courses retreived successfully!";
			$response["type"] = "courses";
			$response["body"] = $Courses;
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
	 * Serach for a particular course
	 *
	 * @return CoursesHandler
	 */
	public function searchCourses(): CoursesHandler
	{
		try {
			DB::beginTransaction();

			$perPage = $this->request->get("perPage") ?? 100;
			$query = $this->request->get("sq"); /// searchQuery(as sq)

			if (!($Courses = Modules::Courses()->searchCourses($query, $perPage))) {
				return $this->raise(APIResponseMessages::DB_ERROR->value, null, APIResponseCodes::SERVER_ERR->value);
			}

			//-----------------------------------------------------

			/** Request response data */
			$responseMessage = "Success, courses search results.";
			$response["type"] = "courses";
			$response["body"] = $Courses;
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
	 * Delete a course from system
	 *
	 * @param string $id
	 *
	 * @return CoursesHandler
	 */
	public function deleteCourse(string $id): CoursesHandler
	{
		try {
			DB::beginTransaction();

			if (!Modules::Courses()->remove($id)) {
				return $this->raise(APIResponseMessages::DB_ERROR->value, null, APIResponseCodes::SERVER_ERR->value);
			}

			//-----------------------------------------------------

			/** Request response data */
			$responseMessage = "Success, course deleted successfully!";
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
