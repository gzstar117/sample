<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Auth;

class UsersController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth', [
           'except' => ['create','store','index']
        ]);

        $this->middleware('guest', [
            'only' => ['create']
        ]);
    }

    //显示所有的用户的列表
    public function index()
    {
        $users = User::paginate(10);
        return view('users.index', compact('users'));
    }

    //用户注册页面
    public function create()
    {
        return view('users.create');
    }

    //用户个人信息页面
    public function show(User $user)
    {
        $this->authorize('update', $user);
        return view('users.show', compact('user'));
    }

    //执行用户注册
    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => 'required|max:50',
            'email' => 'required|email|unique:users|max:255',
            'password' => 'required|confirmed|min:6'
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
        ]);

        Auth::login($user);

        session()->flash('success', '欢迎，您将在这里开启一段新的旅程~');
        return redirect()->route('users.show', [$user]);
    }

    //用户编辑页面
    public function edit(User $user)
    {
        $this->authorize('update', $user);
        return view('users.edit', compact('user'));
    }

    //执行用户修改
    public function update(User $user, Request $request)
    {
        $this->validate($request, [
           'name' => 'required|max:50',
           'password' => 'nullable|confirmed|min:6'
        ]);

        $this->authorize('update', $user);

        $data['name'] = $request->name;
        if ($request->password){
            $data['password'] = bcrypt($request->password);
        }

        $user->update($data);
        session()->flash('success', '修改个人信息成功');
        return redirect()->route('users.show', $user->id);
    }

    //执行用户删除
    public function destroy(User $user)
    {
        $this->authorize('destroy', $user);
        $user->delete();
        session()->flash('success','成功删除用户!');
        return back();
    }

}
