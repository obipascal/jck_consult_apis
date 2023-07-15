<?php namespace JCKCon\Http\Modules\Settings;

use App\Http\Modules\Core\BaseModule;
use App\Models\Settings\SiteSettings;
use Exception;
use Illuminate\Support\Facades\Log;

use function App\Utilities\random_id;

class SettingsModule
{
	use BaseModule, FAQSettingsModule;

	public function add(array $params): bool|SiteSettings
	{
		try {
			$settingId = random_id();

			if (!$this->hasSettings()) {
				$params["site_id"] = $settingId;
				/* If application has no pre-set settings create one */
				if (!$this->__save(new SiteSettings(), $params)) {
					return false;
				}
				return $this->get($settingId);
			} else {
				/* If the application has pr-set settings update it */
				if (!($Settings = $this->getConfigs())) {
					return false;
				}

				if (!$this->update($Settings->site_id, $params)) {
					return false;
				}

				return $this->get($Settings->site_id);
			}
		} catch (Exception $th) {
			Log::error($th->getMessage(), ["Line" => $th->getLine(), "file" => $th->getFile()]);
			return false;
		}
	}

	public function update(string $id, array $params): bool
	{
		try {
			if (!($setting = $this->get($id))) {
				return false;
			}

			return $this->__update($setting, "site_id", $setting->site_id, $params);
		} catch (Exception $th) {
			Log::error($th->getMessage(), ["Line" => $th->getLine(), "file" => $th->getFile()]);
			return false;
		}
	}

	public function get(string $id): bool|null|SiteSettings
	{
		try {
			return SiteSettings::query()
				->where("site_id", $id)
				->first();
		} catch (Exception $th) {
			Log::error($th->getMessage(), ["Line" => $th->getLine(), "file" => $th->getFile()]);
			return false;
		}
	}

	public function getConfigs(): bool|null|SiteSettings
	{
		try {
			return SiteSettings::query()->first();
		} catch (Exception $th) {
			Log::error($th->getMessage(), ["Line" => $th->getLine(), "file" => $th->getFile()]);
			return false;
		}
	}

	public function remove(string $id): bool
	{
		try {
			if (!($settings = $this->get($id))) {
				return false;
			}

			return $this->__delete($settings, "site_id", $settings->site_id);
		} catch (Exception $th) {
			Log::error($th->getMessage(), ["Line" => $th->getLine(), "file" => $th->getFile()]);
			return false;
		}
	}

	public function hasSettings(): bool
	{
		try {
			return !empty(SiteSettings::query()->first());
		} catch (Exception $th) {
			Log::error($th->getMessage(), ["Line" => $th->getLine(), "file" => $th->getFile()]);
			return false;
		}
	}
}