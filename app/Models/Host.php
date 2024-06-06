<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Arr;

class Host extends Model
{
    use HasFactory;
    protected $fillable = [
        'address',
        'task_id'
    ];

    protected $casts = [
        'info' => 'array',
        'created_at' => 'datetime:Y-m-d H:i',
        'updated_at' => 'datetime:Y-m-d H:i',
    ];

    public function task() : BelongsTo
    {
        return $this->belongsTo(Task::class);
    }

    public function scopeCompleted(Builder $qb)
    {
        return $qb->whereNotNull('info');
    }

    public function getHostAttribute()
    {
        return Arr::first(explode(":", $this->address));
    }

    public function getPortAttribute()
    {
        if(!str_contains($this->address, ":")) {
            return null;
        }
        return Arr::last(explode(":", $this->address));
    }
}
