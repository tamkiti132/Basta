<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Report extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'contribute_user_id',
        'type',
        'reason',
        'detail',
    ];



    public function contribute_user()
    {
        return $this->belongsTo(User::class, 'contribute_user_id');
    }


    public function user()
    {
        return $this->belongsTo(User::class, 'user_type_report_links', 'report_id', 'user_id');
    }

    public function memo()
    {
        return $this->belongsTo(Memo::class, 'memo_type_report_links', 'report_id', 'memo_id');
    }

    public function comment()
    {
        return $this->belongsTo(Comment::class, 'comment_type_report_links', 'report_id', 'comment_id');
    }

    public function group()
    {
        return $this->belongsTo(Group::class, 'group_type_report_links', 'report_id', 'group_id');
    }

    public function groups()
    {
        return $this->belongsToMany(Group::class, 'group_type_report_links');
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'user_type_report_links');
    }

    public function memos()
    {
        return $this->belongsToMany(Memo::class, 'memo_type_report_links');
    }

    public function comments()
    {
        return $this->belongsToMany(Comment::class, 'comment_type_report_links');
    }
}
