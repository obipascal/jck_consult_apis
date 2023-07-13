<?php namespace JCKCon\Enums;

enum APIResponseCodes: int
{
	case SERVER_ERR = 500;
	case CLIENT_ERR = 400;
	case TECHNICAL_ERR = 422;
	case UNAUTHORIZED = 403;
	case NOT_FOUND = 404;
}
