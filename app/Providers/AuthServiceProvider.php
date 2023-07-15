<?php

namespace App\Providers;

// use Illuminate\Support\Facades\Gate;

use App\Models\Users\User;
use App\Policies\UserPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
	/**
	 * The model to policy mappings for the application.
	 *
	 * @var array<class-string, class-string>
	 */
	protected $policies = [
		User::class => UserPolicy::class,
	];

	/**
	 * Register any authentication / authorization services.
	 */
	public function boot(): void
	{
		$this->registerPolicies();

		foreach ($this->policies as $model => $policy) {
			$definedPolicies = get_class_methods($policy);

			foreach ($definedPolicies as $method) {
				Gate::define($method, [$policy, $method]);
			}
		}
	}
}