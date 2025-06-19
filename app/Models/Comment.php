<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'memo_id',
        'comment',
    ];

    public function memo()
    {
        return $this->belongsTo(Memo::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function reports()
    {
        return $this->belongsToMany(
            Report::class,
            'comment_type_report_links', // 中間テーブルの名前
            'comment_id', // Commentモデルに対応する外部キー
            'report_id' // Reportモデルに対応する外部キー
        );
    }
}
