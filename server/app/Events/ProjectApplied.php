<?php

namespace App\Events;

use App\Models\Project;
use App\Models\User;
use DateTime;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ProjectApplied implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $applyRequest;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(User $appliedUser, Project $project)
    {
        $tmp = [
            'userId' => $appliedUser->id,
            'project_id' => $project->id,
            'applied_at' => new DateTime('now'),
            'is_accepted' => null,
            'is_read' => false,
        ];

        $this->applyRequest = json_encode($tmp);
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new Channel('project-applied');
    }
}
