<?php namespace JCKCon\Http\Modules\Users;

use App\Http\Modules\Core\BaseModule;
use App\Models\Users\User;
use Exception;
use Illuminate\Support\Facades\Log;

use function App\Utilities\random_id;

class UsersModule
{
	use BaseModule;
	protected function generateAccessToken(User $user, string $role = "admin")
	{
		$Token = explode("|", $user->createToken($role)->plainTextToken)[1];

		$user->access_token = $Token;

		return $user->save();
	}

	public function add(array $params): bool|User
	{
		try {
			$account_id = random_id();
			$params["account_id"] = $account_id;

			if (!$this->__save(new User(), $params)) {
				return false;
			}

			return $this->get($account_id);
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
				->first();
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
}
