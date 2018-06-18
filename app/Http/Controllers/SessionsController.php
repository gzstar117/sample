<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;

class SessionsController extends Controller
{
    public function __construct()
    {
        $this->middleware('guest', [
            'only' => ['create'],
        ]);
    }

    //登陆页面
    public function create()
    {
        return view('sessions.create');
    }

    //执行登陆
    public function store(Request $request)
    {
        $credentials = $this->validate($request, [
            'email' => 'required|email|max:255',
            'password' => 'required'
        ]);

        if (Auth::attempt($credentials, session()->has('remember'))) {
            session()->flash('success', '欢迎回来');
            return redirect()->intended(route('users.show', Auth::user()));
        } else {
            session()->flash('danger', '很抱歉，您的邮箱和密码不匹配');
            return redirect()->back();
        }

    }

    //执行登出
    public function destroy()
    {
        Auth::logout();
        session()->flash('success', '成功登出');
        return redirect('login');
    }
}
