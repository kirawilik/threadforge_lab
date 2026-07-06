<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\StorePostRequest;
USE App\Http\Resources\PostResource;
use App\Ai\Agents\PostGenerator;
use App\Models\Post;
use App\Enums\PostStatus;
use App\Jobs\GeneratePostJob;


class ContentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return PostResource::collection(
            auth()->user()
                ->posts()
                ->latest()
                ->get()
        );
    }

    /**
     * Store a newly created post.
     */
    public function repurpose(StorePostRequest $request)
    {
     $post = Post::create([

        'user_id' => auth()->id(),

        'blueprint_id' => $request->validated('blueprint_id'),

        'raw_content' => $request->validated('raw_content'),

        'status' => PostStatus::Pending,

    ]);

    GeneratePostJob::dispatch($post);

    return response()->json([
        'message' => 'Post generation queued.',
        'post_id' => $post->id,
    ], 202);
    }

    /**
     * Display one post.
     */
    public function show(Post $post)
    {
        return new PostResource($post);
    }

    /**
     * Update a post.
     */
    public function update(StorePostRequest $request, Post $post)
    {
        $post->update($request->validated());

        return new PostResource($post);
    }

    /**
     * Delete a post.
     */
    public function destroy(Post $post)
    {
        $post->delete();

        return response()->json([
            'message' => 'Post deleted successfully.'
        ]);
    }
}
