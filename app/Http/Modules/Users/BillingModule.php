<?php namespace JCKCon\Http\Modules\Users;

use App\Models\Users\BillingInfo;
use Exception;
use Illuminate\Support\Facades\Log;

trait BillingModule
{



	public function addBillingInfo(string $accountId, array $params): bool|BillingInfo
	{
		try {


			if (!$this->__save(new BillingInfo(), $params)) {
				return false;
			}

			return $this->getBillingInfo($accountId);
		} catch (Exception $th) {
			Log::error($th->getMessage(), ["Line" => $th->getLine(), "file" => $th->getFile()]);
			return false;
		}
	}

	public function updateBillingInfo(string $id, array $params): bool
	{
		try {
			if (!($BillingInfo = $this->getBillingInfo($id))) {
				return false;
			}

			return $this->__update($BillingInfo, "account_id", $BillingInfo->account_id, $params);
		} catch (Exception $th) {
			Log::error($th->getMessage(), ["Line" => $th->getLine(), "file" => $th->getFile()]);
			return false;
		}
	}

	public function getBillingInfo(string $id): bool|null|BillingInfo
	{
		try {
			return BillingInfo::query()
				->where("account_id", $id)
				->first();
		} catch (Exception $th) {
			Log::error($th->getMessage(), ["Line" => $th->getLine(), "file" => $th->getFile()]);
			return false;
		}
	}

	public function removeBillingInfo(string $id): bool
	{
		try {
			if (!($BillingInfo = $this->getBillingInfo($id))) {
				return false;
			}

			return $this->__delete($BillingInfo, "account_id", $BillingInfo->account_id);
		} catch (Exception $th) {
			Log::error($th->getMessage(), ["Line" => $th->getLine(), "file" => $th->getFile()]);
			return false;
		}
	}
}
