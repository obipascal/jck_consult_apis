<?php namespace JCKCon\Enums;

enum UsersPermissions: string
{
	case CREATE = "create";
	case UPDATE = "update";
	case DELETE = "delete";
	case VIEW = "view";
	case PUBLISH = "publish";

	public static function toArray()
	{
		$values = [];

		foreach (self::cases() as $props) {
			array_push($values, $props->value);
		}

		return $values;
	}
}
