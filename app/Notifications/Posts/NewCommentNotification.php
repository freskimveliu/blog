<?php

namespace App\Notifications\Posts;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Messages\SlackMessage;

class NewCommentNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public $comment ;
    public function __construct($comment)
    {
        $this->comment = $comment;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable){
        return ['slack'];
    }


    public function toSlack($notifiable){
        $comment = $this->comment;

        return (new SlackMessage)
            ->from(env('APP_NAME'), ':ghost:')
            ->content('Someone wrote on your post.')
            ->attachment(function ($attachment) use ($comment) {
                $attachment->thumb($comment->post->image_url ?? '');
                $attachment->title($comment->post->title, env('APP_URL')."/posts/$comment->post_id");
                $attachment->content('Comment: '.$comment->message ?? '');
            });
    }
}
