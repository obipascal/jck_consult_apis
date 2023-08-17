<?php namespace JCKCon\Http\Modules\Analysis;

use App\Http\Modules\Core\BaseModule;
use App\Models\Courses\Courses;
use App\Models\Transaction\Transactions;
use App\Models\Users\User;
use Exception;
use Illuminate\Support\Facades\Log;
use JCKCon\Enums\TransStatus;

class AnalysisModule
{
	use BaseModule;

	public function getTotalUsers(): bool|int
	{
		try {
			return User::all()->count();
		} catch (Exception $th) {
			Log::error($th->getMessage(), ["Line" => $th->getLine(), "file" => $th->getFile()]);
			return false;
		}
	}

	public function getTotalCourses(): bool|int
	{
		try {
			return Courses::all()->count();
		} catch (Exception $th) {
			Log::error($th->getMessage(), ["Line" => $th->getLine(), "file" => $th->getFile()]);
			return false;
		}
	}

	public function getTotalRevenue(): bool|float|int
	{
		try {
			$trans = Transactions::query()
				->where("status", TransStatus::SUCCESS->value)
				->get();

			$totalRevenue = 0;

			foreach ($trans as $tran) {
				$totalRevenue = $totalRevenue + $tran->amount;
			}

			return $totalRevenue;
		} catch (Exception $th) {
			Log::error($th->getMessage(), ["Line" => $th->getLine(), "file" => $th->getFile()]);
			return false;
		}
	}
}