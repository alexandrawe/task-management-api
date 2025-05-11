<?php

namespace App\Notifications;

use App\Mail\TaskOverdue as MailTaskOverdue;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TaskOverdue extends Notification
{
    use Queueable;

    protected $task;
    protected $user;

    /**
     * Create a new notification instance.
     */
    public function __construct($task, $user)
    {
        $this->task = $task;
        $this->user = $user;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Erinnerung: Überfällige Aufgabe')
            ->greeting('Hallo -- name --')
            ->markdown('mail.task.overdue', [
                'name' => $this->user->name,
                'task' => [
                    'title' => $this->task->title,
                    'deadline' => Carbon::parse($this->task->deadline)->format('d.m.Y \u\m H:i \U\h\r'),
                ]
            ]);
    }
}
