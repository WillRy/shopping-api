<?php

namespace CodeShopping\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;


class PhoneNumberChangeMail extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $url;
    private $token;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($user, $token)
    {
        $this->user = $user;
        $this->token = $token;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $this->url = route('customers.web_phone_number_update', ['token' => $this->token]);
        return $this->subject('alteracão de número de telefone')->markdown('mails.phone_number_change_email');
    }
}
