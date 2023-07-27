<?php

return [
	"secret_key" => env("STRIPE_SCR_KEY"),
	"pub_key" => env("STRIPE_PUB_KEY"),
	"webhook_secret" => env("STRIPE_WEBHOOK_CLIENT_SECRET"),
	"currency" => env("STRIPE_CURRENCY"),
];
