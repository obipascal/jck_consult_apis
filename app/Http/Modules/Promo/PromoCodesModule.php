<?php namespace JCKCon\Http\Modules\Promo;

use App\Http\Modules\Core\BaseModule;
use App\Models\Promos\PromoCodes;
use Carbon\Carbon;
use Exception;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Support\Facades\Log;
use JCKCon\Http\Modules\Promo\PromoCodeUsageModule;

use function App\Utilities\random_id;
use function App\Utilities\random_string;

class PromoCodesModule
{
	use BaseModule, PromoCodeUsageModule;

	public function add(array $params): bool|PromoCodes
	{
		try {
			$promoId = random_id();
			$params["promo_id"] = $promoId;
			$params["promo_code"] = strtoupper(random_string("alnum"));

			if (!$this->__save(new PromoCodes(), $params)) {
				return false;
			}

			return $this->get($promoId);
		} catch (Exception $th) {
			Log::error($th->getMessage(), ["Line" => $th->getLine(), "file" => $th->getFile()]);
			return false;
		}
	}

	public function update(string $id, array $params): bool
	{
		try {
			if (!($Promo = $this->get($id))) {
				return false;
			}

			return $this->__update($Promo, "promo_id", $Promo->promo_id, $params);
		} catch (Exception $th) {
			Log::error($th->getMessage(), ["Line" => $th->getLine(), "file" => $th->getFile()]);
			return false;
		}
	}

	public function get(string $id): bool|null|PromoCodes
	{
		try {
			return PromoCodes::query()
				->where("promo_id", $id)
				->orWhere("promo_code", $id)
				->first();
		} catch (Exception $th) {
			Log::error($th->getMessage(), ["Line" => $th->getLine(), "file" => $th->getFile()]);
			return false;
		}
	}

	public function getPromoCodes(int $perPage = 50): bool|LengthAwarePaginator
	{
		try {
			return PromoCodes::query()
				->latest()
				->paginate($perPage);
		} catch (Exception $th) {
			Log::error($th->getMessage(), ["Line" => $th->getLine(), "file" => $th->getFile()]);
			return false;
		}
	}

	public function remove(string $id): bool
	{
		try {
			if (!($Promo = $this->get($id))) {
				return false;
			}

			return $this->__delete($Promo, "promo_id", $Promo->promo_id);
		} catch (Exception $th) {
			Log::error($th->getMessage(), ["Line" => $th->getLine(), "file" => $th->getFile()]);
			return false;
		}
	}

	public function hasExpired(string $id): bool
	{
		try {
			if (!($Promo = $this->get($id))) {
				return false;
			}

			/* valid to is always in the future, so if now is now in the future then to is in
			 the pass therefore rendering it expired */
			$ValidTo = Carbon::createFromDate($Promo->valid_to);
			$now = Carbon::now();

			return $now > $ValidTo;
		} catch (Exception $th) {
			Log::error($th->getMessage(), ["Line" => $th->getLine(), "file" => $th->getFile()]);
			return false;
		}
	}
}
