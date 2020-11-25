<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class notificaUsuarios extends Mailable
{
    use Queueable, SerializesModels;
    public $email, $producto, $comentario;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($email, $producto, $comentario)
    {
        $this->email = $email;
        $this->producto = $producto;
        $this->comentario = $comentario;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('correos.notificaUsuarios');
    }
}
