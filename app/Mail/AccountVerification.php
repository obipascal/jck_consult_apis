<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class AccountVerification extends Mailable
{
	use Queueable, SerializesModels;

	public $layout_header = "default";
	/**
	 * Create a new message instance.
	 */
	public function __construct(public string $code)
	{
		//
	}

	/**
	 * Get the message envelope.
	 */
	public function envelope(): Envelope
	{
		return new Envelope(subject: "Account Verification");
	}

	/**
	 * Get the message content definition.
	 */
	public function content(): Content
	{
		return new Content(view: "email.accountVerification");
	}

	/**
	 * Get the attachments for the message.
	 *
	 * @return array<int, \Illuminate\Mail\Mailables\Attachment>
	 */
	public function attachments(): array
	{
		return [];
	}
}
