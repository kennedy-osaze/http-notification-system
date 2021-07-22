<?php

namespace App\Actions;

use App\Models\Subscriber;
use App\Models\Topic;
use Illuminate\Support\Facades\DB;

class CreateSubscription
{
    /**
     * Create subscriptions to a topic by a url
     */
    public function create(string $topic, string $url): Topic
    {
        return DB::transaction(function () use ($topic, $url) {
            $topic = Topic::firstOrCreate([
                'name' => Topic::normalizeName($topic)
            ]);

            $topic->subscribers()->syncWithoutDetaching(
                Subscriber::firstOrCreate(['url' => $url])
            );

            return $topic;
        });
    }
}
