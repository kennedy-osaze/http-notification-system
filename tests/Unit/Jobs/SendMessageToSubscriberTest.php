<?php

use App\Jobs\SendMessageToSubscriber;
use App\Models\Subscriber;
use App\Models\Topic;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;

class SendMessageToSubscriberTest extends TestCase
{
    public function testTopicCanBePublishToSubscriber()
    {
        Http::fake(['http://localhost' => Http::response(null, 200)]);

        $topic = new Topic(['name' => 'topic1']);
        $subscriber = new Subscriber(['url' => 'http://localhost']);
        $message = ['message' => 'Hello World!'];

        $job = new SendMessageToSubscriber($topic, $message, $subscriber);

        $job->handle();

        Http::assertSent(function (Request $request) {
            return $request->url() === 'http://localhost';
        });
    }
}
