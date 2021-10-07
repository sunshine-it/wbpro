<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

// 用户策略类
class UserPolicy
{
    use HandlesAuthorization;

    /**
     * Create a new policy instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    // 个人只能对自身编辑 第一个参数默认为当前登录用户实例，第二个参数则为要进行授权的用户实例
    public function update(User $currentUser, User $user)
    {
        return $currentUser->id === $user->id;
    }

    // 删除策略
    public function destroy(User $currentUser, User $user)
    {
        return $currentUser->is_admin && $currentUser->id !== $user->id;
    }
}
