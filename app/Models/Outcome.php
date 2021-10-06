<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Outcome extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = ['name', 'exit_code', 'output'];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'exit_code' => 'integer',
    ];

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'outcomes';

    /**
     * Session relation.
     *
     * @return BelongsTo
     */
    public function session(): BelongsTo
    {
        return $this->belongsTo(Session::class);
    }

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
     * Task relation.
     *
     * @return BelongsTo
     */
    public function task(): BelongsTo
    {
        return $this->belongsTo(Task::class);
    }

    /**
     * Step relation.
     *
     * @return BelongsTo
     */
    public function step(): BelongsTo
    {
        return $this->belongsTo(Step::class);
    }
}
