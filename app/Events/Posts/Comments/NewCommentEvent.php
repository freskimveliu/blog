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

class NewCommentEvent implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     *
     * @return void
     */

    public $comment;
    public function __construct($comment)
    {
        $this->comment = $comment;
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
        return 'new-comment';
    }

    public function broadcastWith(){
        return [
            'id'            => $this->comment->id,
            'message'       => $this->comment->message,
            'user'          => $this->comment->user ?? null,
            'user_name'     => $this->comment->user_name,
            'created_at'    => $this->comment->created_at,
        ];
    }
}
