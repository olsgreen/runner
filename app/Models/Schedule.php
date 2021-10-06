<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Schedule extends Model
{
    const NOTIFY_FAILURE = 'failure',
          NOTIFY_ALL     = 'all',
          NOTIFY_NONE    = 'none';
    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = ['definition', 'notify', 'email'];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'args' => 'json',
    ];

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'schedules';

    /**
     * Story relation.
     *
     * @return BelongsTo
     */
    public function story(): BelongsTo
    {
        return $this->belongsTo(Story::class);
    }
}
