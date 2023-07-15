<?php namespace App\Http\Config;

/**
 * API Response
 */
trait RESTResponse
{
	private bool $STATE = false;
	private string $ERROR;
	private string $MESSAGE;
	private $RESPONSE;
	private int $STATUSCODE = 200;

	use RESTState;

	/**
	 * Set the API operation success status
	 *
	 * @param mixed $response The response data  to send to client.
	 * @param string $message The message to pass as the response message
	 * @param bool $state The state of the operation which will be hte status of the
	 * api call.
	 * @param int $statusCode The status code to notify the client of the operation.
	 */
	private function sendResponse($response, string $message, bool $state = true, int $statusCode = 200)
	{
		if (!empty($response)) {
			$this->RESPONSE = $response;
		}

		if (!empty($message)) {
			$this->MESSAGE = $message;
		}

		$this->STATE = $state;
		if (!empty($statusCode)) {
			$this->STATUSCODE = $statusCode;
		}

		return $this->__sendAPIResponse();
	}

	/**
	 * Set the API operation error state
	 *
	 * @param string $error The error message indicating what went wrong.
	 * @param mixed $response The response data to send along with te response to the client.
	 * @param int $statusCode The client status code to notify the browser.
	 *
	 */
	private function terminateRequest(string $error = null, $response = null, int $statusCode = 400)
	{
		if (!empty($error)) {
			$this->ERROR = $error;
		}

		$this->RESPONSE = $response ? $response : $this->RESPONSE;

		if (!empty($statusCode)) {
			$this->STATUSCODE = $statusCode;
		}

		$this->STATE = false;

		return $this->__sendAPIResponse();
	}

	/**
	 * Send response to client
	 *
	 * @return void
	 */
	private function __sendAPIResponse()
	{
		return response()->json(
			[
				"status" => $this->STATE,
				"message" => $this->STATE === true ? $this->MESSAGE : $this->ERROR,
				"resource" => $this->RESPONSE["type"] ?? ($this->STATE === true ? "global_resource" : null),
				"data" => $this->RESPONSE["body"] ?? null,
			],
			$this->STATUSCODE
		);
	}
}