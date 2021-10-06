<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Session extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'sessions';

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = ['name'];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'finished_at' => 'date',
        'aggregate_exit_code' => 'integer',
    ];

    /**
     * Story relation.
     *
     * @return BelongsTo
     */
    public function story(): BelongsTo
    {
        return $this->belongsTo(Story::class);
    }

    /**
     * Outcomes relation.
     *
     * @return HasMany
     */
    public function outcomes(): HasMany
    {
        return $this->hasMany(Outcome::class);
    }
}
