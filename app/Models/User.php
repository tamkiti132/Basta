<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Laravel\Jetstream\HasProfilePhoto;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Support\Str;

class User extends Authenticatable
{
    use HasApiTokens;
    use HasFactory;
    use HasProfilePhoto;
    use Notifiable;
    // use TwoFactorAuthenticatable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'email',
        'password',
        'nickname',
        'username',
        'suspension_state',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        // 'two_factor_recovery_codes',
        // 'two_factor_secret',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        // 'email_verified_at' => 'datetime',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array<int, string>
     */
    protected $appends = [
        'profile_photo_url',
    ];

    public function memo()
    {
        return $this->HasMany(Memo::class);
    }

    public function goods()
    {
        return $this->belongsToMany(Memo::class, 'goods');
    }

    public function laterReads()
    {
        return $this->belongsToMany(Memo::class, 'later_reads');
    }

    public function comment()
    {
        return $this->HasMany(comment::class);
    }

    public function target_report()
    {
        return $this->hasMany(Report::class, 'contribute_user_id');
    }

    public function reports()
    {
        return $this->belongsToMany(
            Report::class,
            'user_type_report_links', // 中間テーブルの名前
            'user_id', // Userモデルに対応する外部キー
            'report_id' // Reportモデルに対応する外部キー
        );
    }

    public function userReports()
    {
        return $this->hasMany(User_type_report_link::class, 'user_id');
    }

    public function memoReports()
    {
        return $this->hasMany(Memo_type_report_link::class, 'user_id');
    }

    public function commentReports()
    {
        return $this->hasMany(Comment_type_report_link::class, 'user_id');
    }

    public function groupReports()
    {
        return $this->hasMany(Group_type_report_link::class, 'user_id');
    }

    public function roles()
    {
        return $this->hasMany(Role::class, 'user_id');
    }

    public function groupRoles()
    {
        return $this->belongsToMany(Group::class, 'roles')->withPivot('role');
    }

    public function blockedGroup()
    {
        return $this->belongsToMany(Group::class, 'block_states', 'user_id', 'group_id');
    }
}
