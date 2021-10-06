<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Step extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = ['name', 'script', 'runner', 'order'];

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'steps';

    /**
     * Task relationship.
     *
     * @return BelongsTo
     */
    public function task(): BelongsTo
    {
        return $this->belongsTo(Task::class);
    }
}
