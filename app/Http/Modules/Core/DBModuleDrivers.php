<?php namespace App\Http\Modules\Core;

use Exception;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

/**
 * The database module drivers
 */
trait DBModuleDrivers
{
	/**
	 * Save new datbase record.
	 *
	 * @param \Illuminate\Database\Eloquent\Model|mixed $Model
	 * @param array $data
	 * @param string|null $returnKey
	 *
	 * @return bool|string
	 */
	public function __save(Model $Model, array $data, string $returnKey = null): bool|string
	{
		try {
			if (empty($data) && !is_object($Model)) {
				return false;
			}

			foreach ($data as $field => $value) {
				$Model->$field = is_array($value) ? json_encode($value) : $value;
			}

			if (!$Model->save()) {
				return false;
			}

			if (!empty($returnKey)) {
				return $Model->$returnKey;
			}

			return true;
		} catch (Exception $th) {
			Log::error($th->getMessage(), ["file" => $th->getFile(), "line" => $th->getLine()]);

			return false;
		}
	}

	/**
	 * Update record
	 *
	 * @param \Illuminate\Database\Eloquent\Model|mixed $Model
	 * @param string $id
	 * @param array $data
	 * @param string|null $returnKey
	 *
	 * @return bool|string
	 */
	public function __update(Model $Model, string $keyName, string $id, array $data, string $returnKey = null): bool|string
	{
		try {
			if (
				!($ModelRecord = $Model
					::query()
					->where($keyName, $id)
					->first())
			) {
				return false;
			}

			if (empty($data)) {
				return false;
			}

			foreach ($data as $field => $value) {
				$ModelRecord->$field = is_array($value) ? json_encode($value) : $value;
			}

			if ($saved = $ModelRecord->save()) {
				if (!empty($returnKey)) {
					return $ModelRecord->$returnKey;
				} else {
					return $saved;
				}
			}

			return false;
		} catch (Exception $th) {
			Log::error($th->getMessage(), ["file" => $th->getFile(), "line" => $th->getLine()]);

			return false;
		}
	}

	/**
	 * Delete a database record
	 *
	 * @param \Illuminate\Database\Eloquent\Model|mixed $Model
	 * @param string|null $keyName
	 * @param string|null $id
	 * @param bool $all
	 *
	 * @return bool
	 */
	public function __delete(Model $Model, string $keyName = null, string $id = null, bool $force = false, bool $all = false): bool
	{
		try {
			if ($all) {
				if ($force) {
					if (!$Model::query()->forceDelete()) {
						return false;
					}
				} else {
					if (!$Model::query()->delete()) {
						return false;
					}
				}

				return true;
			}

			if ($force) {
				if (
					!$Model
						::query()
						->where($keyName, $id)
						->forceDelete()
				) {
					return false;
				}
			} else {
				if (
					!$Model
						::query()
						->where($keyName, $id)
						->delete()
				) {
					return false;
				}
			}

			return true;
		} catch (Exception $th) {
			Log::error($th->getMessage(), ["file" => $th->getFile(), "line" => $th->getLine()]);

			return false;
		}
	}

	/**
	 * Get a single item from database
	 *
	 * @param \Illuminate\Database\Eloquent\Model|mixed $Model
	 * @param string $keyName
	 * @param string $keyValue
	 *
	 * @return \Illuminate\Database\Eloquent\Model|mixed|bool|null
	 */
	public function __getItem(Model $Model, string $keyName, string $keyValue): Model|bool|null
	{
		try {
			return $Model
				::query()
				->where($keyName, $keyValue)
				->first();
		} catch (Exception $th) {
			Log::error($th->getMessage(), ["file" => $th->getFile(), "line" => $th->getLine()]);

			return false;
		}
	}

	/**
	 * Get multiple items from database
	 *
	 * @param \Illuminate\Database\Eloquent\Model|mixed $Model
	 * @param string|null $keyName
	 * @param string|null $keyValue
	 *
	 * @return \Illuminate\Database\Eloquent\Collection|bool
	 */
	public function __getItems(Model $Model, string $keyName = null, string $keyValue = null): Collection|bool
	{
		try {
			return $Model
				::query()
				->where($keyName, $keyValue)
				->latest()
				->get();
		} catch (Exception $th) {
			Log::error($th->getMessage(), ["file" => $th->getFile(), "line" => $th->getLine()]);

			return false;
		}
	}

	/**
	 * Get multiple items from database
	 *
	 * @param \Illuminate\Database\Eloquent\Model|mixed $Model
	 * @param string|null $keyName
	 * @param string|null $keyValue
	 *
	 * @return \Illuminate\Database\Eloquent\Collection|bool
	 */
	public function __getAll(Model $Model): Collection|bool
	{
		try {
			return $Model
				::query()
				->latest()
				->get();
		} catch (Exception $th) {
			Log::error($th->getMessage(), ["file" => $th->getFile(), "line" => $th->getLine()]);

			return false;
		}
	}

	/**
	 * Get multiple items from database with pagination
	 *
	 * @param \Illuminate\Database\Eloquent\Model|mixed $Model
	 * @param string|null $keyName
	 * @param string|null $keyValue
	 * @param int $limit
	 *
	 * @return bool|\Illuminate\Contracts\Pagination\LengthAwarePaginator
	 */
	public function __getPaginatedItems(Model $Model, string $keyName = null, string $keyValue = null, int $limit = 10): bool|LengthAwarePaginator
	{
		try {
			if (empty($keyName) && empty($keyValue)) {
				/* get model items without using where statment */
				return $Model
					::query()
					->latest()
					->paginate($limit);
			}

			return $Model
				::query()
				->where($keyName, $keyValue)
				->latest()
				->paginate($limit);
		} catch (Exception $th) {
			Log::error($th->getMessage(), ["file" => $th->getFile(), "line" => $th->getLine()]);

			return false;
		}
	}
}
