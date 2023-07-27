<?php namespace JCKCon\Http\Modules\Transaction;

use App\Http\Modules\Core\BaseModule;
use App\Models\Transaction\Transactions;
use Exception;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Support\Facades\Log;

use function App\Utilities\random_id;

class TransactionModule
{
	use BaseModule;

	public function add(array $params): bool|null|Transactions
	{
		try {
			if (!$this->hasTrans($params["account_id"], $params["course_id"])) {
				$transId = random_id();
				$params["trans_id"] = $transId;

				if (!$this->__save(new Transactions(), $params)) {
					return false;
				}
				return $this->get($transId);
			}

			if (!($Trans = $this->getUserTransForACourse($params["account_id"], $params["course_id"]))) {
				return false;
			}
			if (!$this->update($Trans->trans_id, $params)) {
				return false;
			}

			return $this->get($Trans->trans_id);
		} catch (Exception $th) {
			Log::error($th->getMessage(), ["Line" => $th->getLine(), "file" => $th->getFile()]);
			return false;
		}
	}

	public function update(string $id, array $params): bool
	{
		try {
			if (!($trans = $this->get($id))) {
				return false;
			}

			return $this->__update($trans, "trans_id", $trans->trans_id, $params);
		} catch (Exception $th) {
			Log::error($th->getMessage(), ["Line" => $th->getLine(), "file" => $th->getFile()]);
			return false;
		}
	}

	public function get(string $id): bool|null|Transactions
	{
		try {
			return Transactions::query()
				->where("trans_id", $id)
				->orWhere("reference", $id)
				->first();
		} catch (Exception $th) {
			Log::error($th->getMessage(), ["Line" => $th->getLine(), "file" => $th->getFile()]);
			return false;
		}
	}

	public function all(int $perPage): bool|LengthAwarePaginator
	{
		try {
			return Transactions::query()
				->latest()
				->paginate($perPage);
		} catch (Exception $th) {
			Log::error($th->getMessage(), ["Line" => $th->getLine(), "file" => $th->getFile()]);
			return false;
		}
	}

	public function usersTransactions(string $accountId, int $perPage = 50): bool|Paginator
	{
		try {
			return Transactions::query()
				->where("account_id", $accountId)
				->latest()
				->simplePaginate($perPage);
		} catch (Exception $th) {
			Log::error($th->getMessage(), ["Line" => $th->getLine(), "file" => $th->getFile()]);
			return false;
		}
	}

	public function delete(string $id): bool
	{
		try {
			if (!($trans = $this->get($id))) {
				return false;
			}

			return $this->__delete($trans, "trans_id", $trans->trans_id);
		} catch (Exception $th) {
			Log::error($th->getMessage(), ["Line" => $th->getLine(), "file" => $th->getFile()]);
			return false;
		}
	}

	// ----------------------> [Helpers]

	public function hasTrans(string $accountId, string $courseId): bool
	{
		try {
			return Transactions::query()
				->where(["account_id" => $accountId, "course_id" => $courseId])
				->exists();
		} catch (Exception $th) {
			Log::error($th->getMessage(), ["Line" => $th->getLine(), "file" => $th->getFile()]);
			return false;
		}
	}

	public function getUserTransForACourse(string $accountId, string $courseId): bool|null|Transactions
	{
		try {
			return Transactions::query()
				->where(["account_id" => $accountId, "course_id" => $courseId])
				->first();
		} catch (Exception $th) {
			Log::error($th->getMessage(), ["Line" => $th->getLine(), "file" => $th->getFile()]);
			return false;
		}
	}

	public function getFromPayIntent(string $pid): bool|null|Transactions
	{
		try {
			return Transactions::query()
				->where("pi_id", $pid)
				->first();
		} catch (Exception $th) {
			Log::error($th->getMessage(), ["Line" => $th->getLine(), "file" => $th->getFile()]);
			return false;
		}
	}
}
