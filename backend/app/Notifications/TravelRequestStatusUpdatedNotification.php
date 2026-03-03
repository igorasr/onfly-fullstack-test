<?php

namespace App\Notifications;

use App\Models\TravelRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TravelRequestStatusUpdatedNotification extends Notification
{
    use Queueable;

    public function __construct(public TravelRequest $travelRequest) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Travel request status updated')
            ->line("Your travel request {$this->travelRequest->id} was updated.")
            ->line("Current status: {$this->travelRequest->status->value}.");
    }

    public function toArray(object $notifiable): array
    {
        return [
            'travel_request_id' => $this->travelRequest->id,
            'status' => $this->travelRequest->status->value,
        ];
    }
}
