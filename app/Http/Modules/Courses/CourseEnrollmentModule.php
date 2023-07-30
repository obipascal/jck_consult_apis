<?php namespace JCKCon\Http\Modules\Courses;

use App\Models\Courses\CourseEnrollments;
use Exception;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Log;

use function App\Utilities\random_id;

trait CourseEnrollmentModule
{
	public function addEnrollment(array $params): bool|CourseEnrollments
	{
		try {
			$enrolmsId = random_id();
			$params["enrollment_id"] = $enrolmsId;

			if (!$this->__save(new CourseEnrollments(), $params)) {
				return false;
			}

			return $this->getEnrollment($enrolmsId);
		} catch (Exception $th) {
			Log::error($th->getMessage(), ["Line" => $th->getLine(), "file" => $th->getFile()]);
			return false;
		}
	}

	public function updateEnrollment(string $id, array $params): bool
	{
		try {
			if (!($enrolms = $this->getEnrollment($id))) {
				return false;
			}

			return $this->__update($enrolms, "enrollment_id", $enrolms->enrollment_id, $params);
		} catch (Exception $th) {
			Log::error($th->getMessage(), ["Line" => $th->getLine(), "file" => $th->getFile()]);
			return false;
		}
	}

	public function getEnrollment(string $id): bool|null|CourseEnrollments
	{
		try {
			return CourseEnrollments::query()
				->where("enrollment_id", $id)
				->first();
		} catch (Exception $th) {
			Log::error($th->getMessage(), ["Line" => $th->getLine(), "file" => $th->getFile()]);
			return false;
		}
	}

	public function getEnrollments(int $perPage = 50): bool|LengthAwarePaginator
	{
		try {
			return CourseEnrollments::query()
				->latest()
				->paginate($perPage);
		} catch (Exception $th) {
			Log::error($th->getMessage(), ["Line" => $th->getLine(), "file" => $th->getFile()]);
			return false;
		}
	}

	public function userEnrollments(string $accountId, int $perPage = 50): bool|LengthAwarePaginator
	{
		try {
			return CourseEnrollments::query()
				->where("account_id", $accountId)
				->latest()
				->with(["course"])
				->paginate($perPage);
		} catch (Exception $th) {
			Log::error($th->getMessage(), ["Line" => $th->getLine(), "file" => $th->getFile()]);
			return false;
		}
	}

	public function deleteEnrollment(string $id): bool
	{
		try {
			if (!($enrolms = $this->getEnrollment($id))) {
				return false;
			}

			return $this->__update($enrolms, "enrollment_id", $enrolms->enrollment_id);
		} catch (Exception $th) {
			Log::error($th->getMessage(), ["Line" => $th->getLine(), "file" => $th->getFile()]);
			return false;
		}
	}

	// -------------------> [Helpers]

	public function isEnrolled(string $user, string $course): bool
	{
		try {
			return CourseEnrollments::query()
				->where(["account_id" => $user, "course_id" => $course])
				->exists();
		} catch (Exception $th) {
			Log::error($th->getMessage(), ["Line" => $th->getLine(), "file" => $th->getFile()]);
			return false;
		}
	}
}
