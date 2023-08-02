<?php namespace JCKCon\Enums;

enum TransStatus: string
{
	case SUCCESS = "success";
	case FAILED = "failed";
	case PENDING = "pending";
	case ERROR = "error";

	// Payment type status
	/**
	 * A transaction was initiated and payment was collected in installments
	 */
	case PARTIAL = "partial";
	/**
	 * A transaction was initiated and payment was collected in full.
	 */
	case FULL = "full";
	/**
	 * A transaction was initiated and payment was collect in half successfully
	 */
	case FIRST_INSTALL = "first_installment";
	/**
	 * A transaction was initiated
	 */
	case INITIATED = "initiated";
}