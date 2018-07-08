<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;
use App\Models\User;

class FollowersController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    //关注某个人
    public function store(User $user)
    {
        //如果当前(登陆)用户要关注自己本身, 则重定向到 /
        if (Auth::user()->id === $user->id) {
            return redirect('/');
        }

        //如果不是已关注的人, 则执行"关注"
        if (!Auth::user()->isFollowing($user->id)) {
            Auth::user()->follow($user->id);
        }

        return redirect()->route('users.show', $user->id);
    }

    public function destroy(User $user)
    {
        //如果当前(登陆)用户要取消关注自己本身, 则重定向到 /
        if (Auth::user()->id === $user->id) {
            return redirect('/');
        }

        //如果是已关注的人, 则执行"取消关注"
        if (Auth::user()->isFollowing($user->id)) {
            Auth::user()->unfollow($user->id);
        }

        return redirect()->route('users.show', $user->id);
    }

}
