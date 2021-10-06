<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\HasMany;

class Environment extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = ['values'];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'values' => 'array',
    ];

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'environments';

    /**
     * 'Values' attribute getter.
     *
     * @return mixed|void
     */
    public function getValuesAttribute()
    {
        if (isset($this->attributes['values'])) {
            return decrypt($this->attributes['values']);
        }
    }

    /**
     * 'Values' attribute setter.
     *
     * @param $values
     */
    public function setValuesAttribute($values)
    {
        $this->attributes['values'] = isset($values) ? encrypt($values) : null;
    }

    /**
     * Stories relationship.
     *
     * @return HasMany
     */
    public function stories(): HasMany
    {
        return $this->hasMany(Story::class);
    }
}
