<?php

namespace App\Http\Controllers;

use App\Jobs\PublishTopicToSubscribers;
use App\Models\Topic;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;

class PublishController extends Controller
{
    /**
     * Handle request to publish some data to subscribers of the given topic
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request, string $topic): JsonResponse
    {
        $this->validateRequest($request);

        if (is_null($topic = Topic::name($topic)->first())) {
            return response()->json(['message' => 'The topic does not exist'], 404);
        }

        dispatch(new PublishTopicToSubscribers($topic, $request->data));

        return response()->json(['message' => 'Topic is being published'], 202);
    }

    /**
     * Validate request to create subscription
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    private function validateRequest(Request $request): void
    {
        $this->validate($request, [
            'data' => ['bail', 'required', 'array', function ($attribute, $value, $fail) {
                if (! Arr::isAssoc($value)) {
                    $fail("The {$attribute} must be a key-value pair");
                }
            }],
        ]);
    }
}
