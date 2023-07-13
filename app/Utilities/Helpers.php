<?php namespace App\Utilities;

use BadFunctionCallException;
use Closure;
use ErrorException;
use Illuminate\Console\Command;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use InvalidArgumentException;
use JCKCon\Enums\UsersPermissions;
use Laminas\Escaper\Escaper;
use NumberFormatter;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

if (!function_exists("genRandomInt")) {
	/** Generate random integer numbers */
	function genRandomInt(int $len = 8): int
	{
		$r_numbers = [];
		for ($i = 0; $i < $len; $i++) {
			array_push($r_numbers, random_int(0, 9));
		}

		return (int) implode("", $r_numbers);
	}
}

if (!function_exists("random_string")) {
	/**
	 * Create a Random String
	 *
	 * Useful for generating passwords or hashes.
	 *
	 * @param string  $type Type of random string.  basic, alpha, alnum, numeric, nozero, md5, sha1, and crypto
	 * @param integer $len  Number of characters
	 *
	 * @return string
	 */
	function random_string(string $type = "alnum", int $len = 8): string
	{
		switch ($type) {
			case "alnum":
			case "numeric":
			case "nozero":
			case "alpha":
				switch ($type) {
					case "alpha":
						$pool = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ";
						break;
					case "alnum":
						$pool = "0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ";
						break;
					case "numeric":
						$pool = "0123456789";
						break;
					case "nozero":
						$pool = "123456789";
						break;
				}

				// @phpstan-ignore-next-line
				return substr(str_shuffle(str_repeat($pool, ceil($len / strlen($pool)))), 0, $len);
			case "md5":
				return md5(uniqid((string) mt_rand(), true));
			case "sha1":
				return sha1(uniqid((string) mt_rand(), true));
			case "crypto":
				return bin2hex(random_bytes($len / 2));
		}
		// 'basic' type treated as default
		return (string) mt_rand();
	}
}

if (!function_exists("getChargeAmount")) {
	/** Get the real amount to be used with the charged api */
	function getChargeAmount(int $amount)
	{
		return $amount * 100;
	}
}

// --------------------------------------------------------------------------------------
if (!function_exists("format_number")) {
	/**
	 * A general purpose, locale-aware, number_format method.
	 * Used by all of the functions of the number_helper.
	 *
	 * @param float       $num
	 * @param integer     $precision
	 * @param string|null $locale
	 * @param array       $options
	 *
	 * @return string
	 */
	function format_number(float $num, int $precision = 1, string $locale = null, array $options = []): string
	{
		// Type can be any of the NumberFormatter options, but provide a default.
		$type = (int) ($options["type"] ?? NumberFormatter::DECIMAL);

		$formatter = new NumberFormatter($locale, $type);

		// Try to format it per the locale
		if ($type === NumberFormatter::CURRENCY) {
			$formatter->setAttribute(NumberFormatter::FRACTION_DIGITS, $options["fraction"]);
			$output = $formatter->formatCurrency($num, $options["currency"]);
		} else {
			// In order to specify a precision, we'll have to modify
			// the pattern used by NumberFormatter.
			$pattern = "#,##0." . str_repeat("#", $precision);

			$formatter->setPattern($pattern);
			$output = $formatter->format($num);
		}

		// This might lead a trailing period if $precision == 0
		$output = trim($output, ". ");

		if (intl_is_failure($formatter->getErrorCode())) {
			throw new BadFunctionCallException($formatter->getErrorMessage());
		}

		// Add on any before/after text.
		if (isset($options["before"]) && is_string($options["before"])) {
			$output = $options["before"] . $output;
		}

		if (isset($options["after"]) && is_string($options["after"])) {
			$output .= $options["after"];
		}

		return $output;
	}
}
// --------------------------------------------------------------------------------------

if (!function_exists("number_to_size")) {
	/**
	 * Formats a numbers as bytes, based on size, and adds the appropriate suffix
	 *
	 * @param mixed   $num       Will be cast as int
	 * @param integer $precision
	 * @param string  $locale
	 *
	 * @return boolean|string
	 */
	function number_to_size($num, int $precision = 1, string $locale = null)
	{
		// Strip any formatting & ensure numeric input
		try {
			$num = 0 + str_replace(",", "", $num); // @phpstan-ignore-line
		} catch (ErrorException $ee) {
			return false;
		}

		// ignore sub part
		$generalLocale = $locale;
		if (!empty($locale) && ($underscorePos = strpos($locale, "_"))) {
			$generalLocale = substr($locale, 0, $underscorePos);
		}

		if ($num >= 1000000000000) {
			$num = round($num / 1099511627776, $precision);
			$unit = __("number.terabyteAbbr", [], $generalLocale);
		} elseif ($num >= 1000000000) {
			$num = round($num / 1073741824, $precision);
			$unit = __("number.gigabyteAbbr", [], $generalLocale);
		} elseif ($num >= 1000000) {
			$num = round($num / 1048576, $precision);
			$unit = __("number.megabyteAbbr", [], $generalLocale);
		} elseif ($num >= 1000) {
			$num = round($num / 1024, $precision);
			$unit = __("number.kilobyteAbbr", [], $generalLocale);
		} else {
			$unit = __("number.bytes", [], $generalLocale);
		}

		return format_number($num, $precision, $locale, ["after" => " " . $unit]);
	}
}

//--------------------------------------------------------------------

if (!function_exists("number_to_currency")) {
	/**
	 * @param float   $num
	 * @param string  $currency
	 * @param string  $locale
	 * @param integer $fraction
	 *
	 * @return string
	 */
	function number_to_currency(float $num, string $currency, string $locale = null, int $fraction = null): string
	{
		return format_number($num, 1, $locale, [
			"type" => NumberFormatter::CURRENCY,
			"currency" => $currency,
			"fraction" => $fraction,
		]);
	}
}

//--------------------------------------------------------------------

if (!function_exists("number_to_amount")) {
	/**
	 * Converts numbers to a more readable representation
	 * when dealing with very large numbers (in the thousands or above),
	 * up to the quadrillions, because you won't often deal with numbers
	 * larger than that.
	 *
	 * It uses the "short form" numbering system as this is most commonly
	 * used within most English-speaking countries today.
	 *
	 * @see https://simple.wikipedia.org/wiki/Names_for_large_numbers
	 *
	 * @param string      $num
	 * @param integer     $precision
	 * @param string|null $locale
	 *
	 * @return boolean|string
	 */
	function number_to_amount($num, int $precision = 0, string $locale = null)
	{
		// Strip any formatting & ensure numeric input
		try {
			$num = 0 + str_replace(",", "", $num); // @phpstan-ignore-line
		} catch (ErrorException $ee) {
			return false;
		}

		$suffix = "";

		// ignore sub part
		$generalLocale = $locale;
		if (!empty($locale) && ($underscorePos = strpos($locale, "_"))) {
			$generalLocale = substr($locale, 0, $underscorePos);
		}

		if ($num > 1000000000000000) {
			$suffix = __("number.quadrillion", [], $generalLocale);
			$num = round($num / 1000000000000000, $precision);
		} elseif ($num > 1000000000000) {
			$suffix = __("number.trillion", [], $generalLocale);
			$num = round($num / 1000000000000, $precision);
		} elseif ($num > 1000000000) {
			$suffix = __("number.billion", [], $generalLocale);
			$num = round($num / 1000000000, $precision);
		} elseif ($num > 1000000) {
			$suffix = __("number.million", [], $generalLocale);
			$num = round($num / 1000000, $precision);
		} elseif ($num > 1000) {
			$suffix = __("number.thousand", [], $generalLocale);
			$num = round($num / 1000, $precision);
		} elseif ($num > 100) {
			$suffix = __("number.naira", [], $generalLocale);
			$num = round($num / 100);
		}

		return format_number($num, $precision, $locale, ["after" => $suffix]);
	}
}

//--------------------------------------------------------------------

if (!function_exists("number_to_roman")) {
	/**
	 * Convert a number to a roman numeral.
	 *
	 * @param string $num it will convert to int
	 *
	 * @return string|null
	 */
	function number_to_roman(string $num): ?string
	{
		$num = (int) $num;
		if ($num < 1 || $num > 3999) {
			return null;
		}

		$_number_to_roman = function ($num, $th) use (&$_number_to_roman) {
			$return = "";
			$key1 = null;
			$key2 = null;
			switch ($th) {
				case 1:
					$key1 = "I";
					$key2 = "V";
					$keyF = "X";
					break;
				case 2:
					$key1 = "X";
					$key2 = "L";
					$keyF = "C";
					break;
				case 3:
					$key1 = "C";
					$key2 = "D";
					$keyF = "M";
					break;
				case 4:
					$key1 = "M";
					break;
			}
			$n = $num % 10;
			switch ($n) {
				case 1:
				case 2:
				case 3:
					$return = str_repeat($key1, $n);
					break;
				case 4:
					$return = $key1 . $key2;
					break;
				case 5:
					$return = $key2;
					break;
				case 6:
				case 7:
				case 8:
					$return = $key2 . str_repeat($key1, $n - 5);
					break;
				case 9:
					$return = $key1 . $keyF; // @phpstan-ignore-line
					break;
			}
			switch ($num) {
				case 10:
					$return = $keyF; // @phpstan-ignore-line
					break;
			}
			if ($num > 10) {
				$return = $_number_to_roman($num / 10, ++$th) . $return;
			}
			return $return;
		};
		return $_number_to_roman($num, 1);
	}
}

//--------------------------------------------------------------------

if (!function_exists("build_img_for_networktransport")) {
	/**
	 * Build and image base64 encode for safe network transport
	 */
	function build_img_for_networktransport(string $filePath)
	{
		if (!Storage::exists($filePath)) {
			return $filePath;
		}

		$ext = Storage::mimeType($filePath);
		$type = "base64";
		$content = Storage::get($filePath);
		$file = base64_encode($content);
		$data = "data:{$ext};{$type},$file";

		return $data;
	}
}

if (!function_exists("strastrik")) {
	/**
	 * add astrik to a string and return
	 */
	function strastrik($string)
	{
		if (!empty($string)) {
			if (Str::contains($string, "@")) {
				$extr = explode("@", $string)[0];

				$str = substr($extr, 3, round(strlen($extr) / 2));

				$str_stared = [];
				for ($i = 0; $i < strlen($str); $i++) {
					array_push($str_stared, "*");
				}

				return Str::replace($str, implode("", $str_stared), $string);
			} else {
				$str = substr($string, 3, round(strlen($string) / 2));

				$str_stared = [];
				for ($i = 0; $i < strlen($str); $i++) {
					array_push($str_stared, "*");
				}

				return Str::replace($str, implode("", $str_stared), $string);
			}
		}

		return $string;
	}
}

if (!function_exists("mask_astrik")) {
	function mask_astrick(string $string, int $len = 5)
	{
		$start = strlen($string) - 3;
		return Str::mask($string, "*", -$start, $len);
	}
}

//--------------------------------------------------------------------
/**
 * USSD Bank names
 * ____
 * You can get the bank name by passing the bank code as the array key
 */
defined("USSDBANKS") ||
	define("USSDBANKS", [
		737 => "Guaranty Trust Bank",
		919 => "United Bank of Africa",

		822 => "Sterling Bank",

		966 => "Zenith Bank",

		770 => "Fidelity Bank",
	]);

/**
 * Payve charges token
 */
defined("CHARGE_TOKENS") ||
	define("CHARGE_TOKENS", [
		"Withdrawal" => 5,
		"Deposit" => 5,
		"Transfer" => 1,
		"Snap Pay" => 5,
		"Wallet Transfer" => 0,
		"Payout" => 5,
		"Top Up" => 0,
		"Donation" => 1.5, // 1%
		"Event" => 2, //2 %
		"Contribution" => 100,
		"Bill Payment" => 0,
		"Electricity" => 50,
		"Tv" => 50,
		"Mobile" => 0,
	]);

// ------------------------------------------------------------------------------

if (!function_exists("esc")) {
	/**
	 * Performs simple auto-escaping of data for security reasons.
	 * Might consider making this more complex at a later date.
	 *
	 * If $data is a string, then it simply escapes and returns it.
	 * If $data is an array, then it loops over it, escaping each
	 * 'value' of the key/value pairs.
	 *
	 * Valid context values: html, js, css, url, attr, raw, null
	 *
	 * @param string|array $data
	 * @param string       $context
	 * @param string       $encoding
	 *
	 * @return string|array
	 * @throws InvalidArgumentException
	 */
	function esc($data, string $context = "html", string $encoding = null)
	{
		if (is_array($data)) {
			foreach ($data as &$value) {
				$value = esc($value, $context);
			}
		}

		if (is_string($data)) {
			$context = strtolower($context);

			// Provide a way to NOT escape data since
			// this could be called automatically by
			// the View library.
			if (empty($context) || $context === "raw") {
				return $data;
			}

			if (!in_array($context, ["html", "js", "css", "url", "attr"], true)) {
				throw new InvalidArgumentException("Invalid escape context provided.");
			}

			$method = $context === "attr" ? "escapeHtmlAttr" : "escape" . ucfirst($context);

			static $escaper;
			if (!$escaper) {
				$escaper = new Escaper($encoding);
			}

			if ($encoding && $escaper->getEncoding() !== $encoding) {
				$escaper = new Escaper($encoding);
			}

			$data = $escaper->$method($data);
		}

		return $data;
	}
}

// -------------------------------------------------------------------------------

if (!function_exists("http_build_url")) {
	define("HTTP_URL_REPLACE", 1); // Replace every part of the first URL when there's one of the second URL
	define("HTTP_URL_JOIN_PATH", 2); // Join relative paths
	define("HTTP_URL_JOIN_QUERY", 4); // Join query strings
	define("HTTP_URL_STRIP_USER", 8); // Strip any user authentication information
	define("HTTP_URL_STRIP_PASS", 16); // Strip any password authentication information
	define("HTTP_URL_STRIP_AUTH", 32); // Strip any authentication information
	define("HTTP_URL_STRIP_PORT", 64); // Strip explicit port numbers
	define("HTTP_URL_STRIP_PATH", 128); // Strip complete path
	define("HTTP_URL_STRIP_QUERY", 256); // Strip query string
	define("HTTP_URL_STRIP_FRAGMENT", 512); // Strip any fragments (#identifier)
	define("HTTP_URL_STRIP_ALL", 1024); // Strip anything but scheme and host

	// Build an URL
	// The parts of the second URL will be merged into the first according to the flags argument.
	//
	// @param	mixed			(Part(s) of) an URL in form of a string or associative array like parse_url() returns
	// @param	mixed			Same as the first argument
	// @param	int				A bitmask of binary or'ed HTTP_URL constants (Optional)HTTP_URL_REPLACE is the default
	// @param	array			If set, it will be filled with the parts of the composed url like parse_url() would return
	function http_build_url($url, $parts = [], $flags = HTTP_URL_REPLACE, &$new_url = false)
	{
		$keys = ["user", "pass", "port", "path", "query", "fragment"];

		// HTTP_URL_STRIP_ALL becomes all the HTTP_URL_STRIP_Xs
		if ($flags && HTTP_URL_STRIP_ALL) {
			$flags |= HTTP_URL_STRIP_USER;
			$flags |= HTTP_URL_STRIP_PASS;
			$flags |= HTTP_URL_STRIP_PORT;
			$flags |= HTTP_URL_STRIP_PATH;
			$flags |= HTTP_URL_STRIP_QUERY;
			$flags |= HTTP_URL_STRIP_FRAGMENT;
		}
		// HTTP_URL_STRIP_AUTH becomes HTTP_URL_STRIP_USER and HTTP_URL_STRIP_PASS
		elseif ($flags && HTTP_URL_STRIP_AUTH) {
			$flags |= HTTP_URL_STRIP_USER;
			$flags |= HTTP_URL_STRIP_PASS;
		}

		// Parse the original URL,
		// assuming it's a valid url or an array that parse_url returns
		if (is_string($url)) {
			$parse_url = parse_url($url);
		} else {
			$parse_url = (array) $url;
		}

		// Scheme and Host are always replaced
		if (isset($parts["scheme"])) {
			$parse_url["scheme"] = $parts["scheme"];
		}
		if (isset($parts["host"])) {
			$parse_url["host"] = $parts["host"];
		}

		// (If applicable) Replace the original URL with it's new parts
		if ($flags && HTTP_URL_REPLACE) {
			foreach ($keys as $key) {
				if (isset($parts[$key])) {
					$parse_url[$key] = $parts[$key];
				}
			}
		} else {
			// Join the original URL path with the new path
			if (isset($parts["path"]) && $flags && HTTP_URL_JOIN_PATH) {
				if (isset($parse_url["path"])) {
					$parse_url["path"] = rtrim(str_replace(basename($parse_url["path"]), "", $parse_url["path"]), "/") . "/" . ltrim($parts["path"], "/");
				} else {
					$parse_url["path"] = $parts["path"];
				}
			}

			// Join the original query string with the new query string
			if (isset($parts["query"]) && $flags && HTTP_URL_JOIN_QUERY) {
				if (isset($parse_url["query"])) {
					$parse_url["query"] .= "&" . $parts["query"];
				} else {
					$parse_url["query"] = $parts["query"];
				}
			}
		}

		// Strips all the applicable sections of the URL
		// Note: Scheme and Host are never stripped
		foreach ($keys as $key) {
			if ($flags && (int) constant("HTTP_URL_STRIP_" . strtoupper($key))) {
				unset($parse_url[$key]);
			}
		}

		$new_url = $parse_url;

		return (isset($parse_url["scheme"]) ? $parse_url["scheme"] . "://" : "") .
			(isset($parse_url["user"]) ? $parse_url["user"] . (isset($parse_url["pass"]) ? ":" . $parse_url["pass"] : "") . "@" : "") .
			(isset($parse_url["host"]) ? $parse_url["host"] : "") .
			(isset($parse_url["port"]) ? ":" . $parse_url["port"] : "") .
			(isset($parse_url["path"]) ? $parse_url["path"] : "") .
			(isset($parse_url["query"]) ? "?" . $parse_url["query"] : "") .
			(isset($parse_url["fragment"]) ? "#" . $parse_url["fragment"] : "");
	}
}

// -------------------------------------------------------------------------------

if (!function_exists("url_shortener_domain")) {
	/**
	 * Get url shortener domain
	 *
	 * @return string
	 */
	function url_shortener_domain()
	{
		if (env("APP_ENV") === "local" || env("APP_ENV") === "development") {
			return env("SHORTENER_DEV_DOMAIN", "http://localhost:3000");
		} else {
			return env("SHORTENER_PROD_DOMAIN", "https://mypayve.com");
		}
	}
}

if (!function_exists("filterAssocArray")) {
	/**
	 * Filter the given array using the callback provided. The callback function should compare the
	 * values an return an item that passes the test.
	 * ____
	 * The callback will recieve the items as fun ($a,$b) which a and b could be anything object, string etc.
	 * The return result of the computation is an array of values that passes the test.
	 *
	 * @param array $array
	 * @param Closure $filterCallback
	 *
	 * @return array
	 */
	function filterAssocArray(array $array, Closure $filterCallback)
	{
		$result = [];

		if (count($array) <= 1) {
			if (isset($array[0])) {
				array_push($result, $array[0]);
			}

			return $result;
		}

		for ($i = 0; $i < count($array); ) {
			if (is_callable($filterCallback)) {
				if (isset($array[$i + 1])) {
					if ($return = $filterCallback($array[$i], $array[$i + 1])) {
						array_push($result, $return);
					}
				}
			}
			$i++;
		}
		return $result;
	}
}

if (!function_exists("getRequestId")) {
	/**
	 * Get VTPass request id string
	 *
	 * @return string
	 */
	function getRequestId(): string
	{
		return date("YmdHi") . random_string("numeric");
	}
}

if (!function_exists("getMainUrl")) {
	/**
	 * Get main app service endpoint url
	 *
	 * @param string $path
	 *
	 * @return string
	 */
	function getMainUrl(string $path)
	{
		return config("main.url") . $path;
	}
}

if (!function_exists("paystackServiceFee")) {
	/**
	 * Get paystack service / processing fees
	 *
	 * @param float $amount
	 *
	 * @return float
	 */
	function paystackServiceFee(float $amount, bool $cape = false)
	{
		/**
		 * Service Table
		 * -------------
		 * 1. Below 2500 => 1.5%
		 * 2. Above 2500 => 1.5% + 100
		 */

		if ($amount > 2500) {
			$percentage = round($amount * 0.015, 2);
			$total = $percentage + 100;

			/* returned caped total */
			if ($cape) {
				if ($total > 2000) {
					return 2000;
				} else {
					return $total;
				}
			} else {
				return $total;
			}
		} else {
			return round($amount * 0.015, 2);
		}
	}
}

if (!function_exists("paystackCappedAmount")) {
	/**
	 * cape the charge amount to 2000 if the service fee is greater than that value
	 *
	 * @param float $chargeAmount
	 * @param float $amount
	 *
	 * @return float
	 */
	function paystackCappedAmount(float $chargeAmount, float $amount)
	{
		/* Cap amount is NGN2000  for local transaction*/
		$serviceFee = paystackServiceFee($amount);

		if ($serviceFee > 2000) {
			$extras = $serviceFee - 2000;
			return $chargeAmount - $extras;
		}

		return $chargeAmount;
	}
}

if (!function_exists("paystackChargeAmount")) {
	/**
	 * Compute and return the amount to charge customer for card transaction
	 *
	 * @param float $amount
	 *
	 * @return float
	 */
	function paystackChargeAmount(float $amount)
	{
		/* get the service fee */
		$serviceFee = paystackServiceFee($amount);
		$chargeAmount = $serviceFee + $amount;

		return paystackCappedAmount($chargeAmount, $amount);
	}
}

if (!function_exists("paystackTransferAmount")) {
	/**
	 * Get paystack transfer amount with charges calculated
	 *
	 * @param float $amount
	 *
	 * @return float
	 */
	function paystackTransferAmount(float $amount)
	{
		if ($amount <= 5000) {
			return $amount + 10;
		}

		if ($amount > 5000 && $amount <= 50000) {
			return $amount + 25;
		}

		if ($amount > 50000) {
			return $amount + 50;
		}
	}
}

if (!function_exists("sanitizeAmount")) {
	/**
	 * Sanitize amount by rounding up the value to 2-decimal position.
	 */
	function sanitizeAmount(float $amount, string $char = ",")
	{
		$roundedAmount = round($amount, 2);

		return (float) implode("", explode($char, $roundedAmount));
	}
}

if (!function_exists("tranxRef")) {
	/**
	 * Generate a transaction reference
	 *
	 * @return string
	 */
	function tranxRef()
	{
		return random_int(1, 9) . random_string("numeric", 11);
	}
}

if (!function_exists("paystackResponseFees")) {
	function paystackResponseFees(int|float $amount)
	{
		return round($amount / 100, 2);
	}
}

if (!function_exists("encryptMessage")) {
	/**
	 * Safely encrypt a message with password using openssl
	 * which can be shared with friends safely
	 *
	 * @param string $password
	 * @param string $message
	 *
	 * @return bool|string
	 */
	function encryptMessage(string $password, string $message): bool|string
	{
		try {
			if (empty($message) || empty($password)) {
				return false;
			}

			$ivlen = openssl_cipher_iv_length($cipher = "AES-256-CBC");
			$iv = openssl_random_pseudo_bytes($ivlen);
			$ciphertext_raw = openssl_encrypt($message, $cipher, $password, $options = OPENSSL_RAW_DATA, $iv);
			$hmac = hash_hmac("sha256", $ciphertext_raw, $password, $as_binary = true);
			$ciphertext = base64_encode($iv . $hmac . $ciphertext_raw);

			return $ciphertext;
		} catch (\Exception $th) {
			return false;
		}
	}
}

if (!function_exists("decryptMessage")) {
	/**
	 * Safely decrypt the encrypted message with the provided password
	 * that was used when encrypting the message
	 *
	 * @param string $password
	 * @param string $ciphertext
	 *
	 * @return bool|string
	 */
	function decryptMessage(string $password, string $ciphertext): bool|string
	{
		try {
			$c = base64_decode($ciphertext);
			$ivlen = openssl_cipher_iv_length($cipher = "AES-256-CBC");
			$iv = substr($c, 0, $ivlen);
			$hmac = substr($c, $ivlen, $sha2len = 32);
			$ciphertext_raw = substr($c, $ivlen + $sha2len);
			$original_plaintext = openssl_decrypt($ciphertext_raw, $cipher, $password, $options = OPENSSL_RAW_DATA, $iv);
			$calcmac = hash_hmac("sha256", $ciphertext_raw, $password, $as_binary = true);
			if (hash_equals($hmac, $calcmac)) {
				// timing attack safe comparison
				return $original_plaintext;
			}

			return false;
		} catch (\Exception $th) {
			return false;
		}
	}
}

if (!function_exists("maskWalletAddress")) {
	/**
	 * Mask a certian potion of a wallet address
	 *
	 * @param string $walletAddress
	 *
	 * @return string
	 */
	function maskWalletAddress(string $walletAddress)
	{
		$start = strlen(explode(".", $walletAddress)[0]);

		return Str::mask($walletAddress, "*", $start, 6);
	}
}

if (!function_exists("formatDataUnits")) {
	/**
	 * Form the data plans units by size
	 *
	 * @param string $value
	 *
	 * @return string|int
	 */
	function formatDataUnits(string $value)
	{
		$mb_pattern = "{\d+mb}";
		$gb_pattern = "{\d+gb}";
		$fgb_pattern = "{\d+.\d+gb}";

		if (!empty($value)) {
			/* for none fractional inputs */
			if (!str_contains($value, ".")) {
				if (preg_match($mb_pattern, strtolower($value), $matches)) {
				} elseif (preg_match($gb_pattern, strtolower($value), $matches)) {
				} else {
					return $value;
				}

				return isset($matches[0]) ? strtoupper($matches[0]) : 0;
			} /*Fractional values */ else {
				if (preg_match($fgb_pattern, strtolower($value), $matches)) {
				} else {
					return $value;
				}

				return isset($matches[0]) ? strtoupper($matches[0]) : 0;
			}
		}

		return 0;
	}
}

if (!function_exists("formatDataDuration")) {
	/**
	 * Format the data plan days durations
	 *
	 * @param string $value
	 *
	 * @return int|string
	 */
	function formatDataDuration(string $value)
	{
		$h_pattern = "{\d+hrs}";
		$hs_pattern = "{\d+ hrs}";
		$dy_pattern = "{\d+day}";
		$dys_pattern = "{\d+days}";
		$dysp_pattern = "{\d+ day}";
		$dyxsp_pattern = "{\d+ days}";
		$hr_pattern = "{\d+hour}";
		$hrs_pattern = "{\d+hours}";
		$hrsp_pattern = "{\d+ hour}";
		$hrxsp_pattern = "{\d+ hours}";

		if (!empty($value)) {
			if (preg_match($h_pattern, strtolower($value), $matches)) {
			} elseif (preg_match($hs_pattern, strtolower($value), $matches)) {
			} elseif (preg_match($dy_pattern, strtolower($value), $matches)) {
			} elseif (preg_match($dys_pattern, strtolower($value), $matches)) {
			} elseif (preg_match($dyxsp_pattern, strtolower($value), $matches)) {
			} elseif (preg_match($dysp_pattern, strtolower($value), $matches)) {
			} elseif (preg_match($hr_pattern, strtolower($value), $matches)) {
			} elseif (preg_match($hrs_pattern, strtolower($value), $matches)) {
			} elseif (preg_match($hrsp_pattern, strtolower($value), $matches)) {
			} elseif (preg_match($hrxsp_pattern, strtolower($value), $matches)) {
			} else {
				return 0;
			}

			return isset($matches[0]) ? $matches[0] : 0;
		}

		return 0;
	}
}

if (!function_exists("formatCableTVProduct")) {
	/**
	 * Format cable tv product name, extract the numerical value from the name
	 *
	 * @param string $value
	 *
	 * @return string|null
	 */
	function formatCableTVProduct(string $value)
	{
		$plain_pattern = "{n\d+}";
		$com_pattern = "{n\d+,\d+}";
		$dot_pattern = "{n\d+.\d+}";
		$com_dot_pattern = "{n\d+,\d+.\d+}";

		if (empty($value)) {
			return null;
		}

		if (!str_contains($value, ",")) {
			if ($formattedValue = preg_replace($plain_pattern, "", strtolower($value))) {
			} else {
				return $value;
			}
		} else {
			if ($formattedValue = preg_replace($com_pattern, "", strtolower($value))) {
				# code...
			} elseif ($formattedValue = preg_replace($dot_pattern, "", strtolower($value))) {
				# code...
			} elseif ($formattedValue = preg_replace($com_dot_pattern, "", strtolower($value))) {
				# code...
			} else {
				return $value;
			}
		}

		return ucwords(str_replace(" - ", "", $formattedValue));
	}
}

if (!function_exists("random_id")) {
	/**
	 * Generate a random numeric ID
	 *
	 * @param int $len the length of the id. NOTE: A positive integer value will be prepend to the provided length.
	 *
	 * @return string
	 */
	function random_id(int $len = 12): string
	{
		return random_int(1, 9) . random_string("numeric", $len);
	}
}

if (!function_exists("permission_migrations")) {
	/**
	 * Run system permissions migrations
	 *
	 * @return void
	 */
	function permission_migrations(Command $command)
	{
		$perms = UsersPermissions::toArray();
		$sys_default_roles = ["admin", "user"];

		foreach ($perms as $perm) {
			if (!Permission::where("name", $perm)->exists()) {
				Permission::create(["name" => $perm, "guard_name" => "web"]);
			}
		}

		$command->info("-----------------------------");
		$command->info("Migrated permissions successfully!");
		$command->info("-----------------------------");

		foreach ($sys_default_roles as $role) {
			if (!Role::where("name", $role)->exists()) {
				Role::create(["name" => $role, "guard_name" => "web"]);
			}
		}

		$command->info("-----------------------------");
		$command->info("Migrated roles successfully!");
		$command->info("-----------------------------");

		Role::findByName("admin", "web")->givePermissionTo(Permission::all());
		Role::findByName("user", "web")->givePermissionTo(Permission::all());

		$command->info("-----------------------------");
		$command->info("Completed: Permission given to all roles.");
		$command->info("-----------------------------");
	}
}
