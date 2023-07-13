<?php namespace JCKCon\Enums;

enum APIResponseMessages: string
{
	case DB_ERROR = "Sorry! Unable to process storage service.";
	case OTP_GEN_ERR = "Sorry! Unable generate an OTP Code at the moment.";
	case ACCOUNT_404 = "Sorry! The provided email is not linked to any registered account.";
	case OPS_ABORTED = "Sorry! Unable to complete your request at the moment.";
	case PSWD_OPS_EXP = "Sorry! The password reset operation has expired.";
}
