<?php

namespace App\Models;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use App\Notifications\ResetPassword;
use App\Models\Status;
use Auth;

class User extends Authenticatable
{
    use Notifiable;

    protected $table = 'users';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    public static function boot()
    {
        parent::boot();

        //模型事件
        static::creating(function ($user) {
            $user->activation_token = str_random(30);
        });
    }

    public function gravatar($size = '100')
    {
        $hash = md5(strtolower(trim($this->attributes['email'])));
        return "http://www.gravatar.com/avatar/{$hash}?s={$size}";
    }

    public function sendPasswordResetNotification($token)
    {
        $this->notify(new ResetPassword($token));
    }

    //关联Status模型
    public function statuses()
    {
        return $this->hasMany(Status::class);
    }

    //取得用户的微博(status)的信息流
    public function feed()
    {
        $user_ids = Auth::user()->followings->pluck('id')->toArray();
        array_push($user_ids, Auth::user()->id);

        // with() 是预加载 User模型的数据
        return Status::whereIn('user_id', $user_ids)->with('user')->orderBy('created_at', 'desc');
    }

    //关联 用户-粉丝 模型(根据被关注的人找粉丝)
    public function followers()
    {
        //第三个参数是"被关注的人的ID", 第四个参数是"粉丝的ID", ********  可以理解为: 根据 "被关注的人找粉丝" 来理解记忆这函数的意思
        return $this->belongsToMany(User::class, 'followers', 'user_id', 'follower_id');
    }

    //关联 用户-粉丝 模型(根据粉丝找关注的人)
    public function followings()
    {
        //第三个参数是"粉丝的ID", 第四个参数是"被关注的人的ID", ********  可以理解为: 根据 "根据粉丝找关注的人" 来理解记忆这函数的意思
        return $this->belongsToMany(User::class, 'followers', 'follower_id', 'user_id');
    }


    //执行 - 当前用户去关注某些人, $user_ids 是要关注的人的ID集合
    public function follow($user_ids)
    {
        if (!is_array($user_ids)) {
            $user_ids = compact('user_ids');
        }
        $this->followings()->sync($user_ids, false);
    }

    //执行 - 当前用户去取消关注某些人, $user_ids 是要取消关注的人的ID集合
    public function unfollow($user_ids)
    {
        if (!is_array($user_ids)) {
            $user_ids = compact('user_ids');
        }
        $this->followings()->detach($user_ids);
    }

    //判断当前用户是否关注某一个人, $user_id 是被关注的人的ID
    public function isFollowing($user_id)
    {
        //contains() 意思指判断 $user_id 是否在 当前用户的 关注人列表集合里面
        return $this->followings->contains($user_id);
    }


}
