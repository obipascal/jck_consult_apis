<?php

namespace App\Policies;

use App\Models\Users\User;
use Illuminate\Auth\Access\Response;
use JCKCon\Enums\UsersRoles;

class UserPolicy
{
	/**
	 * Determine whether the user can view any models.
	 */
	public function isAdmin(User $user): bool
	{
		return $user->getRoleNames()[0] === UsersRoles::ADMIN->value;
	}
}