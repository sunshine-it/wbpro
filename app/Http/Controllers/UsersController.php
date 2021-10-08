<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Auth;
use Mail;

class UsersController extends Controller
{
    // 使用中间件
    public function __construct()
    {
        // 未登录用户访问权限
        $this->middleware('auth', [
            'except' => ['show', 'create', 'store', 'index', 'confirmEmail'],
        ]);

        // 只让未登录用户访问注册页面
        $this->middleware('guest',[
            'only' => ['create'],
        ]);

        // 注册限流 规则是一个小时内只能提交 10 次请求
        $this->middleware('throttle:10,60', [
            'only' => ['store']
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

    // 用户列表
    public function index()
    {
        // $users = User::all();
        $users = User::paginate(6);
        // dd(compact('users'));
        return view('users.index', compact('users'));
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
        // Auth::login($user);
        $this->sendEmailConfirmationTo($user);
        session()->flash('success', '验证邮件已发送到你的注册邮箱上，请注意查收。');
        return redirect('/');
        // 注册成功后的友好提示
        // session()->flash('success', '欢迎，您将在这里开启一段新的旅程~');

        // return redirect()->route('users.show', [$user]);
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

    // 删除用户
    public function destroy(User $user)
    {
        // 删除策略
        $this->authorize('destroy', $user);
        $user->delete();
        session()->flash('success', '成功删除用户！');
        return back();
    }

    // 激活邮件发送
    protected function sendEmailConfirmationTo($user)
    {
        $view = 'emails.confirm';
        $data = compact('user');
        // $from = 'wbpro@example.com';
        // $name = 'Tom';
        $to = $user->email;
        $subject = "感谢注册 Wbpro 应用！请确认你的邮箱。";

        // Mail::send($view, $data, function ($message) use ($from, $name, $to, $subject) {
        //     $message->from($from, $name)->to($to)->subject($subject);
        // });
        Mail::send($view, $data, function ($message) use ($to, $subject) {
            $message->to($to)->subject($subject);
        });
    }

    // 激活成功
    public function confirmEmail($token)
    {
        $user = User::where('activation_token', $token)->firstOrFail();
        $user->activated = true;
        $user->activation_token = null;
        $user->save();
        // 让用户注册激活成功后自动登录
        Auth::login($user);
        // 注册激活成功后的友好提示
        session()->flash('success', '欢迎，您将在这里开启一段新的旅程~');
        return redirect()->route('users.show', [$user]);
    }
}
