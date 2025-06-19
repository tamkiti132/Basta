<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Web_type_feature extends Model
{
    use HasFactory;

    protected $fillable = [
        'url',
    ];

    public function memo()
    {
        return $this->belongsTo(Memo::class);
    }
}
