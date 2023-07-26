<?php namespace JCKCon\Http\Modules\Promo;

use App\Models\Promos\PromoCodeUsage;
use Exception;
use Illuminate\Support\Facades\Log;

trait PromoCodeUsageModule
{
	public function addCodeUsage(string $promoId, string $accountId, array $params): bool|null|PromoCodeUsage
	{
		try {
			$param["promo_id"] = $promoId;
			$param["account_id"] = $accountId;
			$params = [...$param, ...$params];

			if (!$this->codeUsageExists($promoId, $accountId)) {
				if (!$this->__save(new PromoCodeUsage(), $params)) {
					return false;
				}
			}

			return $this->getCodeUsage($promoId, $accountId);
		} catch (Exception $th) {
			Log::error($th->getMessage(), ["Line" => $th->getLine(), "file" => $th->getFile()]);
			return false;
		}
	}

	public function updateCodeUsage(string $id, string $accountId, array $param): bool
	{
		try {
			if (!($Usage = $this->getCodeUsage($id, $accountId, $param))) {
				return false;
			}

			foreach ($param as $field => $value) {
				$Usage->$field = $value;
			}

			return $Usage->save();
		} catch (Exception $th) {
			Log::error($th->getMessage(), ["Line" => $th->getLine(), "file" => $th->getFile()]);
			return false;
		}
	}

	public function getCodeUsage(string $promoId, string $accountId): bool|null|PromoCodeUsage
	{
		try {
			return PromoCodeUsage::query()
				->where(["account_id" => $accountId, "promo_id" => $promoId, "status" => "applied"])
				->first();
		} catch (Exception $th) {
			Log::error($th->getMessage(), ["Line" => $th->getLine(), "file" => $th->getFile()]);
			return false;
		}
	}

	public function isCodeUsed(string $promoId, string $accountId): bool
	{
		try {
			if (!($Promo = $this->getCodeUsage($promoId, $accountId))) {
				return false;
			}

			return $Promo->status === "used";
		} catch (Exception $th) {
			Log::error($th->getMessage(), ["Line" => $th->getLine(), "file" => $th->getFile()]);
			return false;
		}
	}

	public function isCodeApplied(string $promoId, string $accountId): bool
	{
		try {
			if (!($Promo = $this->getCodeUsage($promoId, $accountId))) {
				return false;
			}

			return $Promo->status === "applied";
		} catch (Exception $th) {
			Log::error($th->getMessage(), ["Line" => $th->getLine(), "file" => $th->getFile()]);
			return false;
		}
	}

	public function codeUsageExists(string $promoId, string $accountId): bool
	{
		try {
			return PromoCodeUsage::query()
				->where(["account_id" => $accountId, "promo_id" => $promoId])
				->exists();
		} catch (Exception $th) {
			Log::error($th->getMessage(), ["Line" => $th->getLine(), "file" => $th->getFile()]);
			return false;
		}
	}
}
