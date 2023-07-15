<?php namespace JCKCon\Http\Handlers\Courses;

use App\Http\Handlers\Core\BaseHandler;
use App\Http\Modules\Modules;
use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class CoursesHandler
{
	use BaseHandler;

	protected function processImageUpload(string $id): bool|string
	{
		try {
			$siteLogo = $this->request->file("image");

			if (!$siteLogo->isValid()) {
				return false;
			}

			/* check if there's an existing site logo already */
			$Course = Modules::Courses()->get($id);
			if (!empty($Course->image)) {
				$path = Str::replace(config("app.url") . "/", "", $Course->image);
				$path = Str::replace("storage", "public", $path);

				if (Storage::exists($path)) {
					Storage::delete($path);
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

	public function create()
	{
		try {
			DB::beginTransaction();

			$params = $this->request->all(["title", "desc", "price", "body"]);

			//-----------------------------------------------------

			/** Request response data */
			$responseMessage = "Success";
			$response["type"] = "";
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
