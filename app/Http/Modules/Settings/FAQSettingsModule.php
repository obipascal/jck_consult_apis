<?php namespace JCKCon\Http\Modules\Settings;

use App\Models\Settings\FAQs;
use Exception;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Support\Facades\Log;

use function App\Utilities\random_id;

trait FAQSettingsModule
{
	public function addFAQ(array $params): bool|FAQs
	{
		try {
			$settingId = random_id();
			$params["faq_id"] = $settingId;

			if (!$this->__save(new FAQs(), $params)) {
				return false;
			}

			return $this->getFAQ($settingId);
		} catch (Exception $th) {
			Log::error($th->getMessage(), ["Line" => $th->getLine(), "file" => $th->getFile()]);
			return false;
		}
	}

	public function updateFAQ(string $id, array $params): bool
	{
		try {
			if (!($setting = $this->getFAQ($id))) {
				return false;
			}

			return $this->__update($setting, "faq_id", $setting->faq_id, $params);
		} catch (Exception $th) {
			Log::error($th->getMessage(), ["Line" => $th->getLine(), "file" => $th->getFile()]);
			return false;
		}
	}

	public function getFAQ(string $id): bool|null|FAQs
	{
		try {
			return FAQs::query()
				->where("faq_id", $id)
				->first();
		} catch (Exception $th) {
			Log::error($th->getMessage(), ["Line" => $th->getLine(), "file" => $th->getFile()]);
			return false;
		}
	}

	public function getAllFAQ(int $perPage = 50): bool|Paginator
	{
		try {
			return FAQs::query()
				->latest()
				->simplePaginate($perPage);
		} catch (Exception $th) {
			Log::error($th->getMessage(), ["Line" => $th->getLine(), "file" => $th->getFile()]);
			return false;
		}
	}

	public function removeFAQ(string $id): bool
	{
		try {
			if (!($settings = $this->getFAQ($id))) {
				return false;
			}

			return $this->__delete($settings, "faq_id", $settings->faq_id);
		} catch (Exception $th) {
			Log::error($th->getMessage(), ["Line" => $th->getLine(), "file" => $th->getFile()]);
			return false;
		}
	}
}