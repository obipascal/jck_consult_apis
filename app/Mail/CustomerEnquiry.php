<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class CustomerEnquiry extends Mailable
{
	use Queueable, SerializesModels;

	public $layout_header = "enquiry";
	/**
	 * Create a new message instance.
	 */
	public function __construct(public string $name, public string $email, public string $phoneNumber, public string $enquiryMessage, public string $enquirySubject)
	{
		//
	}

	/**
	 * Get the message envelope.
	 */
	public function envelope(): Envelope
	{
		return new Envelope(subject: "Customer Enquiry: {$this->enquirySubject}", replyTo: $this->email);
	}

	/**
	 * Get the message content definition.
	 */
	public function content(): Content
	{
		return new Content(view: "email.enquiry");
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
