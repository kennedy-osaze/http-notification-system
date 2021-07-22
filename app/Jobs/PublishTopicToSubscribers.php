<?php

namespace App\Jobs;

use App\Models\Topic;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class PublishTopicToSubscribers implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels;

    public array $message;

    public Topic $topic;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Topic $topic, array $message)
    {
        $this->topic = $topic;
        $this->message = $message;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $this->topic->subscribers()->chunkById(1000, function ($subscribers) {
            $subscribers->each(function ($subscriber) {
                dispatch(new SendMessageToSubscriber($this->topic, $this->message, $subscriber));
            });
        });
    }
}
