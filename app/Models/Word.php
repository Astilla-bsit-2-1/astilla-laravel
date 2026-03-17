<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Word extends Model
{
    protected $fillable = ['category_id', 'word'];

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function hints(): HasMany
    {
        return $this->hasMany(Hint::class);
    }
}
