<?php namespace JCKCon\Http\Modules\Misc;

use App\Models\Misc\Enquiries;
use Exception;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Log;

use function App\Utilities\random_id;

trait EnquiriesModule
{
	public function saveEnquiry(array $params): bool|Enquiries
	{
		try {
			$contactId = random_id();
			$params["contact_id"] = $contactId;
			if (!$this->__save(new Enquiries(), $params)) {
				return false;
			}

			return $this->getEnquiry($contactId);
		} catch (Exception $th) {
			Log::error($th->getMessage(), ["Line" => $th->getLine(), "file" => $th->getFile()]);
			return false;
		}
	}

	public function updateEnquiry(string $id, array $params): bool
	{
		try {
			if (!($Enquiry = $this->getEnquiry($id))) {
				return false;
			}

			return $this->__update($Enquiry, "contact_id", $Enquiry->contact_id, $params);
		} catch (Exception $th) {
			Log::error($th->getMessage(), ["Line" => $th->getLine(), "file" => $th->getFile()]);
			return false;
		}
	}

	public function getEnquiry(string $id): bool|null|Enquiries
	{
		try {
			return Enquiries::query()
				->where("contact_id", $id)
				->first();
		} catch (Exception $th) {
			Log::error($th->getMessage(), ["Line" => $th->getLine(), "file" => $th->getFile()]);
			return false;
		}
	}

	public function getEnquiries(int $perPage = 50): bool|LengthAwarePaginator
	{
		try {
			return Enquiries::query()
				->latest()
				->paginate($perPage);
		} catch (Exception $th) {
			Log::error($th->getMessage(), ["Line" => $th->getLine(), "file" => $th->getFile()]);
			return false;
		}
	}

	public function removeEnquiry(string $id): bool
	{
		try {
			if (!($Enquiry = $this->getEnquiry($id))) {
				return false;
			}

			return $this->__delete($Enquiry, "contact_id", $Enquiry->contact_id);
		} catch (Exception $th) {
			Log::error($th->getMessage(), ["Line" => $th->getLine(), "file" => $th->getFile()]);
			return false;
		}
	}
}
