<?php

use App\Models\Subscriber;
use App\Models\Topic;
use Laravel\Lumen\Testing\DatabaseMigrations;

class SubscriptionControllerTest extends TestCase
{
    use DatabaseMigrations;

    public function testSubscriptionCanBeCreatedWithNewTopicAndSubscriber()
    {
        $payload = ['url' => 'http://localhost'];
        $topicName = 'topic1';

        $response = $this->json('POST', route('subscribe', ['topic' => $topicName]), $payload);

        $response->seeStatusCode(200)->shouldReturnJson([
            'url' => $payload['url'],
            'topic' => $topicName,
        ]);

        $topic = Topic::name($topicName)->first();

        $this->assertNotNull($topic);
        $this->assertTrue(Subscriber::where('url', $payload['url'])->exists());
        $this->assertTrue($topic->subscribers()->where('url', $payload['url'])->exists());
    }

    public function testSubscriberCanSubscribeToAnExistingTopic()
    {
        $payload = ['url' => 'http://localhost'];
        $topic = Topic::factory()->create();

        $this->json('POST', route('subscribe', ['topic' => $topic->name]), $payload)
            ->seeStatusCode(200);

        $subscriber = Subscriber::where('url', $payload['url'])->first();

        $this->assertNotNull($subscriber);
        $this->assertTrue($topic->subscribers()->where('url', $payload['url'])->exists());
    }

    public function testTopicCanHaveMultipleSubscribers()
    {
        $topic = Topic::factory()->has(Subscriber::factory())->create();
        $payload = ['url' => 'http://localhost'];

        $this->json('POST', route('subscribe', ['topic' => $topic->name]), $payload)
            ->seeStatusCode(200);

        $this->assertEquals($topic->subscribers()->count(), 2);

        $this->assertTrue($topic->subscribers()->where('url', $payload['url'])->exists());
    }
}
