<?php namespace JCKCon\Http\Modules\Users;

use App\Http\Modules\Core\BaseModule;
use App\Models\Users\User;
use Carbon\Carbon;
use Exception;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Log;
use Spatie\Permission\Models\Role;

use function App\Utilities\random_id;

class UsersModule
{
	use BaseModule, BillingModule, OTPModule;

	protected function generateAccessToken(User $user, string $role = "admin")
	{
		$Token = explode("|", $user->createToken($role)->plainTextToken)[1];

		$user->access_token = $Token;

		/* assign roles to user */
		$sysRole = Role::where("name", $role)->first();
		$user->assignRole($sysRole);

		return $user->save();
	}

	public function resetAccessToken(User $User, $role = "admin"): bool|User
	{
		$User->tokens()->delete();

		$Token = explode("|", $User->createToken($role)->plainTextToken)[1];

		$User->access_token = $Token;

		if (!$User->save()) {
			return false;
		}

		return $this->get($User->account_id);
	}

	public function add(array $params, string $role = "admin"): bool|string|User
	{
		try {
			if (!($account_id = $this->__save(new User(), $params, "account_id"))) {
				return false;
			}

			if (!($NewUser = $this->get($account_id))) {
				return false;
			}

			$this->generateAccessToken($NewUser, $role);

			return match ($role) {
				"user" => $this->generateOTPCode($NewUser),
				"admin" => $NewUser,
				default => $NewUser,
			};
		} catch (Exception $th) {
			Log::error($th->getMessage(), ["Line" => $th->getLine(), "file" => $th->getFile()]);
			return false;
		}
	}

	public function update(string $id, array $params): bool
	{
		try {
			if (!($User = $this->get($id))) {
				return false;
			}

			return $this->__update($User, "account_id", $User->account_id, $params);
		} catch (Exception $th) {
			Log::error($th->getMessage(), ["Line" => $th->getLine(), "file" => $th->getFile()]);
			return false;
		}
	}

	public function get(string $id): bool|null|User
	{
		try {
			return User::query()
				->where("account_id", $id)
				->orWhere("email", $id)
				->with("enrollments", fn($query) => $query->with("course"))
				->first();
		} catch (Exception $th) {
			Log::error($th->getMessage(), ["Line" => $th->getLine(), "file" => $th->getFile()]);
			return false;
		}
	}

	public function all(string $except = null): bool|Collection
	{
		try {
			return User::query()
				->whereNot("account_id", $except)
				->latest()
				->get();
		} catch (Exception $th) {
			Log::error($th->getMessage(), ["Line" => $th->getLine(), "file" => $th->getFile()]);
			return false;
		}
	}

	public function paginate(string $except, int $perPage = 50): bool|LengthAwarePaginator
	{
		try {
			return User::query()
				->latest()
				->whereNot("account_id", $except)
				->paginate($perPage);
		} catch (Exception $th) {
			Log::error($th->getMessage(), ["Line" => $th->getLine(), "file" => $th->getFile()]);
			return false;
		}
	}
	public function remove(string $id): bool
	{
		try {
			if (!($User = $this->get($id))) {
				return false;
			}

			return $this->__delete($User, "account_id", $User->account_id);
		} catch (Exception $th) {
			Log::error($th->getMessage(), ["Line" => $th->getLine(), "file" => $th->getFile()]);
			return false;
		}
	}

	public function exists(string $id): bool
	{
		try {
			return User::query()
				->where("account_id", $id)
				->orWhere("email", $id)
				->exists();
		} catch (Exception $th) {
			Log::error($th->getMessage(), ["Line" => $th->getLine(), "file" => $th->getFile()]);
			return false;
		}
	}
}
