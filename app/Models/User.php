<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Support\Str;
use Auth;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $table = 'users';

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    public static function boot()
    {
        parent::boot();
        static::creating(function ($user){
            $user->activation_token = Str::random(10);
        });
    }

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * 用户头像 Gravatar
     * size
     */
    public function gravatar($size = '100')
    {
        // $hash = md5(strtolower(trim($this->attributes['email'])));
        $hash = strtolower(trim($this->attributes['name']));
        return "https://ui-avatars.com/api?name=$hash&size=$size&background=random";
    }

    public function statuses()
    {
        // 一个用户拥有多条微博
        return $this->hasMany(Status::class);
    }

    // 动态流原型
    public function feed()
    {
        // return $this->statuses()
        //         ->orderBy('created_at', 'desc');
        $user_ids = $this->followings->pluck('id')->toArray();
        array_push($user_ids, $this->id);
        return Status::whereIn('user_id', $user_ids)
                                ->with('user')
                                ->orderBy('created_at', 'desc');
    }

    //以粉丝为例，一个用户能够拥有多个粉丝(粉丝关系)
    public function followers()
    {
        return $this->belongsToMany(User::class, 'followers', 'user_id', 'follower_id');
    }

    // 用户关注
    public function followings()
    {
        return $this->belongsToMany(User::class, 'followers', 'follower_id', 'user_id');
    }

    // 定义关注
    public function follow($user_ids)
    {
        if ( ! is_array($user_ids)) {
            $user_ids = compact('user_ids');
        }
        $this->followings()->sync($user_ids, false);
    }

    // 定义取消关注
    public function unfollow($user_ids)
    {
        if ( ! is_array($user_ids)) {
            $user_ids = compact('user_ids');
        }
        $this->followings()->detach($user_ids);
    }

    // 判断是否关注
    public function isFollowing($user_id)
    {
        return $this->followings->contains($user_id);
    }

}
