<?php namespace JCKCon\Http\Modules\Courses;

use App\Http\Modules\Core\BaseModule;
use App\Models\Courses\Courses;
use Exception;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Support\Facades\Log;

use function App\Utilities\random_id;

class CoursesModule
{
	use BaseModule, CourseMaterialModule, CourseEnrollmentModule;

	public function add(array $params): bool|Courses
	{
		try {
			$courseId = random_id();
			$params["course_id"] = $courseId;

			if (!$this->__save(new Courses(), $params)) {
				return false;
			}

			return $this->get($courseId);
		} catch (Exception $th) {
			Log::error($th->getMessage(), ["Line" => $th->getLine(), "file" => $th->getFile()]);
			return false;
		}
	}

	public function update(string $id, array $params): bool
	{
		try {
			if (!($Course = $this->get($id))) {
				return false;
			}

			return $this->__update($Course, "course_id", $Course->course_id, $params);
		} catch (Exception $th) {
			Log::error($th->getMessage(), ["Line" => $th->getLine(), "file" => $th->getFile()]);
			return false;
		}
	}

	public function get(string $id): bool|null|Courses
	{
		try {
			return Courses::query()
				->where("course_id", $id)
				->first();
		} catch (Exception $th) {
			Log::error($th->getMessage(), ["Line" => $th->getLine(), "file" => $th->getFile()]);
			return false;
		}
	}

	public function getCourses(string $status = "all", int $perPage = 50): bool|Paginator
	{
		try {
			$query = Courses::query();
			return match ($status) {
				"drafted" => $query
					->where("status", "drafted")
					->latest()
					->simplePaginate($perPage),
				"published" => $query
					->where("status", "published")
					->latest()
					->simplePaginate($perPage),
				default => $query->latest()->simplePaginate($perPage),
			};
		} catch (Exception $th) {
			Log::error($th->getMessage(), ["Line" => $th->getLine(), "file" => $th->getFile()]);
			return false;
		}
	}

	public function remove(string $id): bool
	{
		try {
			if (!($Course = $this->get($id))) {
				return false;
			}

			return $this->__delete($Course, "course_id", $Course->course_id);
		} catch (Exception $th) {
			Log::error($th->getMessage(), ["Line" => $th->getLine(), "file" => $th->getFile()]);
			return false;
		}
	}

	public function getPublishedCourses(int $perPage = 50): bool|Paginator
	{
		try {
			return Courses::query()
				->latest()
				->where("status", "published")
				->simplePaginate($perPage);
		} catch (Exception $th) {
			Log::error($th->getMessage(), ["Line" => $th->getLine(), "file" => $th->getFile()]);
			return false;
		}
	}

	public function searchCourses(string $query, int $perPage = 50): bool|Paginator
	{
		try {
			return Courses::query()
				->latest()
				->where("title", "like", "%{$query}%")
				->orWhere("title", "like", "{$query}%")
				->orWhere("title", "like", "%{$query}")
				->simplePaginate($perPage);
		} catch (Exception $th) {
			Log::error($th->getMessage(), ["Line" => $th->getLine(), "file" => $th->getFile()]);
			return false;
		}
	}
}
