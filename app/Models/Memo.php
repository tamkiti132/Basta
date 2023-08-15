<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Memo extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'group_id',
        'title',
        'shortMemo',
        'additionalMemo',
        'type',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function goods()
    {
        return $this->belongsToMany(User::class, 'goods');
    }

    public function laterReads()
    {
        return $this->belongsToMany(User::class, 'later_reads');
    }

    public function labels()
    {
        return $this->belongsToMany(Label::class);
    }

    public function web_type_feature()
    {
        return $this->hasOne(Web_type_feature::class);
    }

    public function book_type_feature()
    {
        return $this->hasOne(Book_type_feature::class, 'memo_id');
    }

    public function comment()
    {
        return $this->hasMany(Comment::class);
    }

    public function reports()
    {
        return $this->belongsToMany(
            Report::class,
            'memo_type_report_links', // 中間テーブルの名前
            'memo_id', // Memoモデルに対応する外部キー
            'report_id' // Reportモデルに対応する外部キー
        );
    }

    // 検索ロジック
    public function scopeSearch($query, $search)
    {
        if ($search !== null) {
            $search_split = mb_convert_kana($search, 's');
            $search_split2 = preg_split('/[\s]+/', $search_split);

            foreach ($search_split2 as $value) {
                $query->where('title', 'like', '%' . $value . '%')
                    ->orWhere('shortMemo', 'like', '%' . $value . '%');
            }
        }
        return $query;
    }
}
