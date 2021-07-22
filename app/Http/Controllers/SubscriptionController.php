<?php

namespace App\Http\Controllers;

use App\Actions\CreateSubscription;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SubscriptionController extends Controller
{
    /**
     * Handle request to subscribe to a topic
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request, CreateSubscription $subscription): JsonResponse
    {
        $this->validateRequest($request);

        $topic = $subscription->create($request->topic, $request->url);

        return response()->json([
            'url' => $request->url,
            'topic' => $topic->name,
        ]);
    }

    /**
     * Validate request to create subscription
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    private function validateRequest(Request $request): void
    {
        $request->merge(['topic' => $request->topic]);

        $this->validate($request, [
            'topic' => 'required|string|min:2|max:255',
            'url' => 'required|url|max:255',
        ]);
    }
}
