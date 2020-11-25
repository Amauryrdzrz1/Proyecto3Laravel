<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Prophecy\Util\StringUtil;

class CorreoRegistro extends Mailable
{
    use Queueable, SerializesModels;

    Public $email;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(string $email)
    {
        //
        $this->email = $email;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('correos.CorreoRegistro');
    }
}
