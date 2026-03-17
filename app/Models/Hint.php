<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Hint extends Model
{
    protected $fillable = ['word_id', 'hint'];

    public function word(): BelongsTo
    {
        return $this->belongsTo(Word::class);
    }
}
