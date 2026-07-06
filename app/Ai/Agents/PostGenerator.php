<?php

namespace App\Ai\Agents;

use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Ai\Contracts\Agent;
use Laravel\Ai\Contracts\Conversational;
use Laravel\Ai\Contracts\HasStructuredOutput;
use Laravel\Ai\Contracts\HasTools;
use Laravel\Ai\Contracts\Tool;
use Laravel\Ai\Messages\Message;
use Laravel\Ai\Promptable;
use Stringable;

class PostGenerator implements Agent, Conversational, HasStructuredOutput, HasTools
{
    use Promptable;

    /**
     * Get the instructions that the agent should follow.
     */
    public function instructions(): Stringable|string
    {
        return <<<TEXT
Tu es un expert en rédaction de publications techniques pour X (Twitter).

À partir d'un contenu brut, tu dois produire :

- un hook accrocheur ;
- une liste de points clés ;
- un score de lisibilité entre 0 et 100 ;
- une liste de hashtags pertinents ;
- une justification expliquant pourquoi le ton respecte le blueprint.
TEXT;
    }
    

    /**
     * Get the list of messages comprising the conversation so far.
     *
     * @return Message[]
     */
    public function messages(): iterable
    {
        return [];
    }

    /**
     * Get the tools available to the agent.
     *
     * @return Tool[]
     */
    public function tools(): iterable
    {
        return [];
    }

    /**
     * Get the agent's structured output schema definition.
     */
    public function schema(JsonSchema $schema): array
    {
       return [

            'hook_propose'
                => $schema->string()->required(),

            'body_points'
                => $schema
                    ->array()
                    ->items($schema->string())
                    ->required(),

            'technical_readability_score'
                => $schema
                    ->integer()
                    ->required(),

            'suggested_hashtags'
                => $schema
                    ->array()
                    ->items($schema->string())
                    ->required(),

            'tone_compliance_justification'
                => $schema
                    ->string()
                    ->required(),

        ]; 
    }
}
