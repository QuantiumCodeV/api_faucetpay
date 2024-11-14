<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class VerificationEmail extends Mailable
{
    use Queueable, SerializesModels;

    public $user;

    /**
     * Создайте новый экземпляр сообщения.
     *
     * @param  \App\Models\User  $user
     * @return void
     */
    public function __construct($user)
    {
        $this->user = $user;
    }

    /**
     * Построить сообщение.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('emails.verification')
                    ->subject('Подтверждение электронной почты')
                    ->with(['user' => $this->user]);
    }
}
