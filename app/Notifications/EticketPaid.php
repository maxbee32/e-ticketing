<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Messages\SlackMessage;

class EticketPaid extends Notification implements ShouldQueue
{
    use Queueable;

    private $details;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(

        public readonly string $ticketId,
        public readonly string $firstName,
        public readonly string $LstName,
        public readonly string $NumberOfTicket,
        public readonly string $Image,
        public readonly string $total,
        public readonly string $Country,
        public readonly string $reservationDate,
    )
    {
        //

        //$this->details = $details;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail', 'database', 'slack'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {

        return (new MailMessage)
                    ->subject('New E-Ticket Recieved.')
                    ->greeting('E-Ticket Received')
                    ->line("{$this->firstName } has reserved and made payment to visit the museum.")
                    ->line("This has been booked for {$this->reservationDate }")
                    ->action('View E-Ticket', url('/'))
                    ->line('Thank you for using our application!');
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
            // 'order_id' => $this->details['order_id']
        ];
    }

    public function toSlack($notifiable){
        return(new SlackMessage)->success()
        ->content("{$this->firstName} just reserved a date for {$this->reservationDate}.");
    }

}
