<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use App\Ai\Agents\PostGenerator;
use App\Enums\PostStatus;
use App\Models\Post;

class GeneratePostJob implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
       
        $response = (new PostGenerator)
            ->prompt($this->post->raw_content);

        $this->post->update([

            'hook_propose' => $response['hook_propose'],

            'body_points' => $response['body_points'],

            'technical_readability_score'
                => $response['technical_readability_score'],

            'suggested_hashtags'
                => $response['suggested_hashtags'],

            'tone_compliance_justification'
                => $response['tone_compliance_justification'],

            'status' => PostStatus::Draft,

        ]);
    }
}
