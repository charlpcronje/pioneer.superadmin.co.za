<?php

namespace Modules\Farmer\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use App\Models\NotificationBinding;
use Modules\Farmer\Services\Firebase;

class MessageSent extends Notification
{
    use Queueable;
    private $notificationData;
    private $firebaseService;
    private $notificationBinding;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($notificationData)
    {
        $this->notificationData = $notificationData;
        $this->firebaseService = new Firebase(config('firebase'));
        $this->notificationBinding = new NotificationBinding();
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param mixed $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail','sms', 'push', 'array','whatsapp'];
    }

    public function toPush($notifiable)
    {

        $identity = md5($notifiable->username);
        $notificationbinding = $this->notificationbinding->find($identity);

        $to = $notificationbinding->sid;

        $message['notification'] = [
            "body"  => $this->notificationData['content'],
            "title" => $this->notificationData['headline']
        ];
        if( $this->notificationData['data'] = 1) {
            $message['data'] = [
                "body"  => $this->notificationData['content'],
                "title" => $this->notificationData['headline']
            ];
        }

        $this->firebaseService->send($message);
    }

    public function toWhatsapp()
    {
        $client = new Client(env('TWILIO_SID'), env('TWILIO_AUTH_TOKEN'));
        $number = substr($notifiable->phone_number, -9);
        $number = "+27".$number;
        $this->client->messages
        ->create("whatsapp:{$number}", // to
           ["from" => "whatsapp:+14155238886", "body" => $this->notificationData['content']]);

    }

    /**
     * 
     */
    public function toSms($notifiable)
    {
        $client = new Client(env('TWILIO_SID'), env('TWILIO_AUTH_TOKEN'));
        $number = substr($notifiable->phone_number, -9);
        $number = "+27".$number;
        $this->client->messages
                    ->create($number, // to
                        ["body" => $this->notificationData['content'], "from" => "+18049448749"]
                    );

    }

    /**
     * Get the mail representation of the notification.
     *
     * @param mixed $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        /*return (new MailMessage)
                    ->line('The introduction to the notification.')
                    ->action('Notification Action', 'https://laravel.com')
                    ->line('Thank you for using our application!');*/

        return (new MailMessage)
                    ->line($this->notificationData['headline'])
                    ->line($this->notificationData['content']);
    }

    /**
     * Get the array representation of the notification.
     *
     * @param mixed $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return $this->notificationData;
    }
}
