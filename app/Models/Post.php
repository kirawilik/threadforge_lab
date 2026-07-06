<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Enums\PostStatus;

class Post extends Model
{
    protected $fillable = [
        'user_id',
        'blueprint_id',
        'raw_content',
        'hook_propose',
        'body_points',
        'technical_readability_score',
        'suggested_hashtags',
        'tone_compliance_justification',
        'status',
    ];
    protected function casts(): array
{
    return [
        'body_points' => 'array',
        'suggested_hashtags' => 'array',
        'status' => PostStatus::class,
    ];
}
public function user()
    {
        return $this->belongsTo(User::class);
    }
 public function blueprint()
    {
        return $this->belongsTo(Blueprint::class);
    }
}
