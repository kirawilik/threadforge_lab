<?php

use App\Models\User;
use Laravel\Sanctum\Sanctum;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('liste les blueprints d un utilisateur authentifie', function () {

    $user = User::factory()->create();

    Sanctum::actingAs($user);

    $user->blueprints()->create([
        'name' => 'Tech Educatif',
        'target_audience' => 'Développeurs Laravel',
        'tone' => 'Professionnel',
        'max_characters' => 280,
        'max_hashtags' => 3,
    ]);

    $response = $this->getJson('/api/blueprints');

    $response
        ->assertStatus(200)
        ->assertJsonStructure([
            'data' => [
                [
                    'id',
                    'name',
                    'target_audience',
                    'tone',
                    'max_characters',
                    'max_hashtags',
                ]
            ]
        ]);
});
it('rejette une requête sans token', function () {

    $response = $this->getJson('/api/blueprints');

    $response->assertStatus(401);

});
it('refuse un blueprint invalide', function () {

    Sanctum::actingAs(User::factory()->create());

    $response = $this->postJson('/api/blueprints', [
        'name' => '',
        'target_audience' => '',
        'tone' => '',
        'max_characters' => '',
        'max_hashtags' => '',
    ]);

    $response
        ->assertStatus(422)
        ->assertJsonValidationErrors([
            'name',
            'target_audience',
            'tone',
            'max_characters',
            'max_hashtags',
        ]);

});