<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Blueprint extends Model
{
  
    protected $fillable = [
        'user_id',
        'name',
        'target_audience',
        'tone',
        'max_characters',
        'max_hashtags',
    ];
    public function user()
{
    return $this->belongsTo(User::class);
}
}

