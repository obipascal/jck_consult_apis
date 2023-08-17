<?php namespace JCKCon\Http\Handlers\Analysis;

use App\Http\Handlers\Core\BaseHandler;
use App\Http\Modules\Modules;
use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class AnalysisHandler
{
	use BaseHandler;

	public function dashboard_analysis()
	{
		try {
			DB::beginTransaction();

			/* Dashboard data */
			$dashData["total_users"] = Modules::Analysis()->getTotalUsers();
			$dashData["total_revenue"] = Modules::Analysis()->getTotalRevenue();
			$dashData["total_courses"] = Modules::Analysis()->getTotalCourses();

			$res_data["dashboard"] = $dashData;
			//-----------------------------------------------------

			/** Request response data */
			$responseMessage = "Success, analysis retrieved";
			$response["type"] = "analysis";
			$response["body"] = $res_data;
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