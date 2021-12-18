<?php

namespace App\Mail;

use App\Models\VerificatedTask;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class VerifyTask extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * The order instance.
     *
     * @var VerificatedTask
     */
    public $task;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(VerificatedTask $task)
    {
        $this->task = $task;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->markdown('vendor.notifications.email')->with($this->task->view());
    }
}
