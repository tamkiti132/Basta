<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Later_read extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'memo_id'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function memo()
    {
        return $this->belongsTo(Memo::class);
    }
}
