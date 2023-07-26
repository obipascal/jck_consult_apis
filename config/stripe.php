<?php

return [
	"secret_key" => env("STRIPE_SCR_KEY"),
	"pub_key" => env("STRIPE_PUB_KEY"),
	"currency" => env("STRIPE_CURRENCY"),
];