<?php

namespace App\Events;

use App\Models\Task;
use Illuminate\Foundation\Events\Dispatchable;

class TaskUpdated
{
    use Dispatchable;

    /**
     * Create a new event instance.
     */
    public function __construct(public Task $task)
    {}
}
