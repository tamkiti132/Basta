<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

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

    public function contribute_user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'contribute_user_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_type_report_links', 'report_id', 'user_id');
    }

    public function memo(): BelongsTo
    {
        return $this->belongsTo(Memo::class, 'memo_type_report_links', 'report_id', 'memo_id');
    }

    public function comment(): BelongsTo
    {
        return $this->belongsTo(Comment::class, 'comment_type_report_links', 'report_id', 'comment_id');
    }

    public function group(): BelongsTo
    {
        return $this->belongsTo(Group::class, 'group_type_report_links', 'report_id', 'group_id');
    }

    public function groups(): BelongsToMany
    {
        return $this->belongsToMany(Group::class, 'group_type_report_links');
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'user_type_report_links');
    }

    public function memos(): BelongsToMany
    {
        return $this->belongsToMany(Memo::class, 'memo_type_report_links');
    }

    public function comments(): BelongsToMany
    {
        return $this->belongsToMany(Comment::class, 'comment_type_report_links');
    }
}
