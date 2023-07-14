<?php

namespace App\Console\Commands;

use App\Http\Modules\Modules;
use App\Mail\AdminConsoleCredentials;
use Carbon\Carbon;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use JCKCon\Enums\APIResponseMessages;
use JCKCon\Enums\UsersRoles;

use function App\Utilities\random_string;

class GenerateAdminCredentials extends Command
{
	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = "app:generate-admin-credentials";

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = "Command to generate administrative credentials";

	/**
	 * Execute the console command.
	 */
	public function handle()
	{
		DB::beginTransaction();
		try {
			$params["email"] = config("admin.email");
			$params["password"] = random_string();
			$params["email_verified_at"] = Carbon::now()->toDateTimeString();
			$params["account_id"] = null;

			if (!($User = Modules::User()->add($params, UsersRoles::ADMIN->value))) {
				$this->error(APIResponseMessages::DB_ERROR->value);

				DB::rollBack();
				DB::commit();

				return self::FAILURE;
			}

			Mail::to($User)->send(new AdminConsoleCredentials($params["email"], $params["password"]));

			$this->info("Admin credentials generated and sent to {$params["email"]} successfully!");

			DB::commit();

			return self::SUCCESS;
		} catch (Exception $th) {
			$this->error($th->getMessage());

			DB::rollBack();
			DB::commit();

			return self::FAILURE;
		}
	}
}