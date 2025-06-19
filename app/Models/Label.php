<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Label extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'group_id',
    ];

    public function groups()
    {
        return $this->belongsTo(Group::class);
    }

    public function memos()
    {
        return $this->belongsToMany(Memo::class);
    }
}
