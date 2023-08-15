<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Memo_type_report_link extends Model
{
    use HasFactory;

    protected $fillable = [
        'report_id',
        'memo_id',
    ];

    public $timestamps = false;

    public function report()
    {
        return $this->belongsTo(Report::class);
    }
}
