<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Auth;

class UsersController extends Controller
{
    // 使用中间件
    public function __construct()
    {
        $this->middleware('auth', [
            'except' => ['show', 'create', 'store'],
        ]);

        // 只让未登录用户访问注册页面
        $this->middleware('guest',[
            'only' => ['create'],
        ]);
    }

    // 用户创建
    public function create()
    {
        return view('users.create');
    }

    // 查看用户
    public function show(User $user)
    {
        return view('users.show', compact('user'));
    }

    // 用户数据验证
    public function store(Request $request)
    {
        // 校验
        $this->validate($request, [
            'name' => 'required|unique:users|max:50',
            'email' => 'required|email|unique:users|max:255',
            'password' => 'required|confirmed|min:6'
        ]);
        // 数据入库
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
        ]);
        // 让用户注册成功后自动登录
        Auth::login($user);
        // 注册成功后的友好提示
        session()->flash('success', '欢迎，您将在这里开启一段新的旅程~');

        return redirect()->route('users.show', [$user]);
    }

    // 编辑用户的操作界面
    public function edit(User $user)
    {
        $this->authorize('update', $user);
        return view('users.edit', compact('user'));
    }

    public function update(User $user, Request $request)
    {
        $this->authorize('update', $user);
        $this->validate($request, [
            'name' => 'required|max:50',
            'password' => 'nullable|confirmed|min:6'
        ]);
        $data = [];
        $data['name'] = $request->name;
        if ($request->password) {
            $data['password'] = bcrypt($request->password);
        }
        $user->update($data);
        session()->flash('success', '个人资料更新成功！');
        return redirect()->route('users.show', $user->id);
    }
}
