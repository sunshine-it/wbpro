<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;

class UsersController extends Controller
{
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
        return ;
    }
}
