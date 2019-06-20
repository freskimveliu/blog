<?php

namespace App\Events\Posts\Comments;

use App\User;
use Illuminate\Broadcasting\Channel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class DeleteCommentEvent implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public $comment;
    public $index;
    public function __construct($comment,$index)
    {
        $this->comment = $comment;
        $this->index   = $index;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn(){
        return new Channel('post.'.$this->comment->post_id);
    }

    public function broadcastAs(){
        return 'delete-comment';
    }

    public function broadcastWith(){
        return [
            'user_name'  => $this->comment->user_name,
            'index'      => $this->index,
        ];
    }
}
