<?php

namespace App\Jobs;

use App\Models\Subscriber;
use App\Models\Topic;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Http\Client\Response;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Throwable;

class SendMessageToSubscriber implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels;

    public array $message;

    public Topic $topic;

    public Subscriber $subscriber;

    public int $backoff = 5;

    public bool $failOnTimeout = true;

    public int $tries = 10;

    public int $timeout = 20;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Topic $topic, array $message, Subscriber $subscriber)
    {
        $this->topic = $topic;
        $this->message = $message;
        $this->subscriber = $subscriber;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $response = $this->send();

        if ($response->failed()) {
            throw new Exception('Message delivery failed');
        }
    }

    /**
     * Handle job failure
     */
    public function failed(Throwable $exception): void
    {
        Log::error($exception->getMessage(), [
            'topic' => $this->topic->name,
            'subscriber_url' => $this->subscriber->url,
            'message' => json_encode($this->message),
        ]);
    }

    /**
     * Send HTTP request to the subscriber
     */
    private function send(): Response
    {
        return Http::timeout(10)
            ->post($this->subscriber->url, $this->payload());
    }

    /**
     * Create the payload to be sent via HTTP
     */
    private function payload(): array
    {
        return [
            'topic' => $this->topic->name,
            'data' => $this->message,
        ];
    }
}
