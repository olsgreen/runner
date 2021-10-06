<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Story extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = ['name'];

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'stories';

    /**
     * Schedule relationship.
     *
     * @return HasOne
     */
    public function schedule(): HasOne
    {
        return $this->hasOne(Schedule::class);
    }

    /**
     * Environment relationship.
     *
     * @return BelongsTo
     */
    public function environment(): BelongsTo
    {
        return $this->belongsTo(Environment::class);
    }

    /**
     * Task relationship.
     *
     * @return HasMany
     */
    public function tasks(): HasMany
    {
        return $this->hasMany(Task::class);
    }

    /**
     * Session relationship.
     *
     * @return HasMany
     */
    public function sessions(): HasMany
    {
        return $this->hasMany(Session::class);
    }
}
