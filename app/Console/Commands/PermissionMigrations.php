<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use function App\Utilities\permission_migrations;

class PermissionMigrations extends Command
{
	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = "app:permission-migrations";

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = "Permisssion migrations";

	/**
	 * Execute the console command.
	 */
	public function handle()
	{
		permission_migrations($this);
		return self::SUCCESS;
	}
}
