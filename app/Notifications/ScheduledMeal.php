<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Log;
use NotificationChannels\WebPush\WebPushMessage;
use NotificationChannels\WebPush\WebPushChannel;

class ScheduledMeal extends Notification implements ShouldQueue
{
    use Queueable;

    public $meal;
    public $timeStart;

    public function __construct($meal, $timeStart)
    {
        $this->meal = $meal;
        $this->timeStart = $timeStart;
    }

    public function getMeal() {
        return $this->meal;
    }

    public function getTimeStart() {
        return $this->timeStart;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return [WebPushChannel::class];
    }

    /**
     * Get the web push message
     *
     * @param  mixed  $notifiable
     * @param  mixed $notification
     * @return \NotificationChannels\WebPush\WebPushMessage
     */
    public function toWebPush($notifiable, $notification)
    {
        $meal = $notification->getMeal();
        $timeStart = $notification->getTimeStart();

        return (new WebPushMessage)
                    ->title("Reserva do '".$meal."'")
                    ->body('Você tem uma reserva de \''.$meal.'\' para ' . $timeStart . '. Se não vai usá-la, essa é a hora de cancelar!');
    }

    public function toArray() {
        return [
            'meal' => $this->meal,
            'timeStart' => $this->timeStart,
        ];
    }
}
