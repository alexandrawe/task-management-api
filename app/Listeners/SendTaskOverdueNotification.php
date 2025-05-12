<?php

namespace App\Listeners;

use App\Events\TaskUpdated;
use App\Models\User;
use App\Notifications\TaskOverdue;
use App\Enum\TaskState;
use Carbon\Carbon;
use Illuminate\Support\Facades\Notification;

class SendTaskOverdueNotification
{
    /**
     * Handle the event.
     */
    public function handle(TaskUpdated $event): void
    {
        $task = $event->task;

        // get user the task is assigned to
        $user = User::find($task->user_id);

        // if task is already done, has no deadline set, deadline is in future or the task has no assigned user
        // then we dont need to send a notification
        if (($task->state !== TaskState::DONE) && (!$task->deadline) || (Carbon::parse($task->deadline)->isFuture()) || (!$user)) {
            return;
        }

        Notification::send($user, new TaskOverdue($task, $user));
    }
}