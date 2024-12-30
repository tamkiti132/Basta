<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Group extends Model
{
    use HasFactory;

    protected $fillable = [
        'group_photo_path',
        'name',
        'introduction',
        'isJoinFreeEnabled',
        'isTipEnabled',
    ];

    public function userRoles()
    {
        return $this->belongsToMany(User::class, 'roles')->withPivot('role');
    }

    //グループの管理者ユーザーのデータだけを返す
    public function managerUser()
    {
        return $this->userRoles()->wherePivot('role', 10);
    }

    //グループの管理者 and サブ管理者ユーザーのデータだけを返す
    public function managerAndSubManagerUser($groupId)
    {
        return $this->userRoles()
            ->wherePivot('role', 10)
            ->orWherePivot('role', 50)
            ->wherePivot('group_id', $groupId);
    }

    public function memos()
    {
        return $this->hasMany(Memo::class);
    }

    public function label()
    {
        return $this->HasMany(Label::class);
    }

    public function comments()
    {
        return $this->hasManyThrough(Comment::class, Memo::class);
    }

    public function blockedUser()
    {
        return $this->belongsToMany(User::class, 'block_states', 'group_id', 'user_id');
    }

    public function reports()
    {
        return $this->belongsToMany(
            Report::class,
            'group_type_report_links', // 中間テーブルの名前
            'group_id', // Groupモデルに対応する外部キー
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
                $query->where('name', 'like', '%' . $value . '%')
                    ->orWhere('introduction', 'like', '%' . $value . '%');
            }
        }
        return $query;
    }
}
