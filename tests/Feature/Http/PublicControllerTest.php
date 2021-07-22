<?php

use App\Jobs\PublishTopicToSubscribers;
use App\Jobs\SendMessageToSubscriber;
use App\Models\Subscriber;
use App\Models\Topic;
use Illuminate\Support\Facades\Queue;
use Laravel\Lumen\Testing\DatabaseMigrations;

class PublishControllerTest extends TestCase
{
    use DatabaseMigrations;

    public function setUp(): void
    {
        parent::setUp();

        Queue::fake();
    }

    public function testInvalidTopicCannotBePublish()
    {
        $invalidTopic = 'invalid-topic';
        $payload = ['data' => ['message' => 'Hello World!']];

        $this->json('POST', route('publish', ['topic' => $invalidTopic]), $payload)
            ->seeStatusCode(404);

        Queue::assertNothingPushed(PublishTopicToSubscribers::class);
    }

    public function testTopicWithNoSubscriberCanBePublished()
    {
        $topicName = 'topic1';
        $topic = Topic::factory()->create(['name' => $topicName]);

        $payload = ['data' => ['message' => 'Hello World!']];

        $this->json('POST', route('publish', ['topic' => $topicName]), $payload)
            ->seeStatusCode(202)
            ->seeJsonStructure(['message']);

        Queue::assertPushed(function (PublishTopicToSubscribers $job) use ($topic, $payload) {
            $this->assertTrue($topic->is($job->topic));
            $this->assertEquals($payload['data'], $job->message);

            return true;
        });

        Queue::assertNotPushed(SendMessageToSubscriber::class);
    }

    public function testTopicWithSubscribersCanBePublished()
    {
        $topicName = 'topic1';
        $topic = Topic::factory()
            ->has(Subscriber::factory()->count(2))
            ->create(['name' => $topicName]);

        $payload = ['data' => ['message' => 'Hello World!']];

        $this->json('POST', route('publish', ['topic' => $topicName]), $payload)
            ->seeStatusCode(202);

        dispatch_now(new PublishTopicToSubscribers($topic, $payload['data']));

        Queue::assertPushed(PublishTopicToSubscribers::class);
        Queue::assertPushed(function (SendMessageToSubscriber $job) use ($topic, $payload) {
            $this->assertTrue($topic->is($job->topic));
            $this->assertTrue($topic->subscribers->contains($job->subscriber));
            $this->assertEquals($payload['data'], $job->message);

            return true;
        });
    }
}
