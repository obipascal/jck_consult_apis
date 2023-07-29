<?php namespace JCKCon\Http\Modules\Reviews;

use App\Http\Modules\Core\BaseModule;
use App\Models\Reviews\CustomerReviews;
use Exception;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Support\Facades\Log;

use function App\Utilities\random_id;

class ReviewsModule
{
	use BaseModule;

	public function add(array $params): bool|null|CustomerReviews
	{
		try {
			$reviewId = random_id();
			$params["review_id"] = $reviewId;

			if (!$this->__save(new CustomerReviews(), $params)) {
				return false;
			}

			return $this->get($reviewId);
		} catch (Exception $th) {
			Log::error($th->getMessage(), ["Line" => $th->getLine(), "file" => $th->getFile()]);
			return false;
		}
	}

	public function update(string $id, array $params): bool
	{
		try {
			if (!($Review = $this->get($id))) {
				return false;
			}

			return $this->__update($Review, "review_id", $Review->review_id, $params);
		} catch (Exception $th) {
			Log::error($th->getMessage(), ["Line" => $th->getLine(), "file" => $th->getFile()]);
			return false;
		}
	}

	public function get(string $id): bool|null|CustomerReviews
	{
		try {
			return CustomerReviews::query()
				->where("review_id", $id)
				->orWhere("reviewer_email", $id)
				->first();
		} catch (Exception $th) {
			Log::error($th->getMessage(), ["Line" => $th->getLine(), "file" => $th->getFile()]);
			return false;
		}
	}

	public function all(int $perPage = 50): bool|LengthAwarePaginator
	{
		try {
			return CustomerReviews::query()
				->latest()
				->paginate($perPage);
		} catch (Exception $th) {
			Log::error($th->getMessage(), ["Line" => $th->getLine(), "file" => $th->getFile()]);
			return false;
		}
	}

	public function published(int $perPage = 50): bool|Paginator
	{
		try {
			return CustomerReviews::query()
				->latest()
				->where("status", "published")
				->simplePaginate($perPage);
		} catch (Exception $th) {
			Log::error($th->getMessage(), ["Line" => $th->getLine(), "file" => $th->getFile()]);
			return false;
		}
	}
	public function remove(string $id): bool
	{
		try {
			if (!($Review = $this->get($id))) {
				return false;
			}

			return $this->__delete($Review, "review_id", $Review->review_id);
		} catch (Exception $th) {
			Log::error($th->getMessage(), ["Line" => $th->getLine(), "file" => $th->getFile()]);
			return false;
		}
	}
}
