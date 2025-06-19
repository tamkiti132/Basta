<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Web_type_feature extends Model
{
    use HasFactory;

    protected $fillable = [
        'url',
    ];

    public function memo(): BelongsTo
    {
        return $this->belongsTo(Memo::class);
    }
}
