<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Book_type_feature extends Model
{
    use HasFactory;

    protected $fillable = [
        'book_photo_path',
    ];

    public function memo()
    {
        return $this->belongsTo(Memo::class, 'memo_id');
    }
}
