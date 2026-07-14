<?php

use App\Jobs\GeneratePostJob;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Laravel\Sanctum\Sanctum;

uses(RefreshDatabase::class);

it('dispatch le job de génération à la soumission', function () {

    Queue::fake();

    $user = User::factory()->create();

    Sanctum::actingAs($user);

    $blueprint = $user->blueprints()->create([
        'name' => 'Test',
        'target_audience' => 'Developers',
        'tone' => 'Professional',
        'max_characters' => 280,
        'max_hashtags' => 3,
    ]);

    $response = $this->postJson('/api/content/repurpose', [
        'blueprint_id' => $blueprint->id,
        'raw_content' => 'Mes notes de Laravel.',
    ]);

    $response->assertStatus(202);

    Queue::assertPushed(GeneratePostJob::class);
});