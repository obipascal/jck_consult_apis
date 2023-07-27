<?php namespace JCKCon\Enums;

enum TransStatus: string
{
	case SUCCESS = "success";
	case FAILED = "failed";
	case PENDING = "pending";
	case ERROR = "error";
}
