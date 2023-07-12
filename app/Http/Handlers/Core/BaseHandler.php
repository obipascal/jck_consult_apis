<?php namespace App\Http\Handlers\Core;

use App\Models\Accounts\User;
use Illuminate\Http\Request;

/**
 * Manage the handlers state and error reporting
 */
trait BaseHandler
{
	public bool $STATE = false;
	public int $CODE = 200;
	public string $ERROR = "";
	public string $MESSAGE = "";
	public mixed $RESPONSE = "";

	protected $request;
	protected array $data;
	public function __construct(Request $request, array $data = [])
	{
		$this->request = $request;
		$this->data = $data;
	}

	/**
	 * Raise an error
	 *
	 * @param string $error
	 * @param mixed $response
	 * @param int $code
	 *
	 */
	protected function raise($error = "Ops! Something went wrong.", $response = null, int $code = 400)
	{
		$this->RESPONSE = $response;
		$this->ERROR = $error;
		$this->STATE = false;
		$this->CODE = $code;

		return $this;
	}

	/**
	 * Return operation response
	 *
	 * @param mixed $response
	 * @param string $message
	 * @param int $code
	 *
	 */
	protected function response($response = null, $message = "Process completed successfully", int $code = 200)
	{
		$this->RESPONSE = $response;
		$this->MESSAGE = $message;
		$this->STATE = true;
		$this->CODE = $code;

		return $this;
	}
}