<?php namespace JCKCon\Http\Modules\Courses;

use App\Models\Courses\CourseMaterials;
use Exception;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Support\Facades\Log;

use function App\Utilities\random_id;

trait CourseMaterialModule
{
	public function addMaterial(array $params): bool|CourseMaterials
	{
		try {
			$materialId = random_id();
			$params["material_id"] = $materialId;

			if (!$this->__save(new CourseMaterials(), $params)) {
				return false;
			}

			return $this->getMaterial($materialId);
		} catch (Exception $th) {
			Log::error($th->getMessage(), ["Line" => $th->getLine(), "file" => $th->getFile()]);
			return false;
		}
	}

	public function updateMaterial(string $id, array $params): bool
	{
		try {
			if (!($Material = $this->getMaterial($id))) {
				return false;
			}

			return $this->__update($Material, "material_id", $Material->material_id, $params);
		} catch (Exception $th) {
			Log::error($th->getMessage(), ["Line" => $th->getLine(), "file" => $th->getFile()]);
			return false;
		}
	}

	public function getMaterial(string $id): bool|null|CourseMaterials
	{
		try {
			return CourseMaterials::query()
				->where("material_id", $id)
				->first();
		} catch (Exception $th) {
			Log::error($th->getMessage(), ["Line" => $th->getLine(), "file" => $th->getFile()]);
			return false;
		}
	}

	public function getMaterials(int $perPage = 50): bool|Paginator
	{
		try {
			return CourseMaterials::query()
				->latest()
				->simplePaginate($perPage);
		} catch (Exception $th) {
			Log::error($th->getMessage(), ["Line" => $th->getLine(), "file" => $th->getFile()]);
			return false;
		}
	}

	public function removeMaterial(string $id): bool
	{
		try {
			if (!($Material = $this->getMaterial($id))) {
				return false;
			}

			return $this->__delete($Material, "material_id", $Material->material_id);
		} catch (Exception $th) {
			Log::error($th->getMessage(), ["Line" => $th->getLine(), "file" => $th->getFile()]);
			return false;
		}
	}
}
