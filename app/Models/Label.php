<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Label extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'group_id',
    ];

    public function groups(): BelongsTo
    {
        return $this->belongsTo(Group::class);
    }

    public function memos(): BelongsToMany
    {
        return $this->belongsToMany(Memo::class);
    }
}
