<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Leonis\Notifications\EasySms\Channels\EasySmsChannel;
use Leonis\Notifications\EasySms\Messages\EasySmsMessage;

class NewPaidOrderSmsNotify extends Notification
{
    use Queueable;

    private $code;
    private $template;

    /**
     * Create a new notification instance.
     *
     * @param $code
     * @param $template
     */
    public function __construct($code, $template)
    {
        $this->code     = $code;
        $this->template = $template;
        //
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return [EasySmsChannel::class];
    }

    public function toEasySms($notifiable)
    {
        return (new EasySmsMessage())
            ->setTemplate($this->template)
            ->setData(['code' => $this->code]);
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            //
        ];
    }
}
