<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Status;
use Auth;

class StaticPagesController extends Controller
{
    public function home()
    {
        $feed_items = [];
        // 检查用户是否已登录
        if (Auth::check()) {
            $feed_items = Auth::user()->feed()->paginate(30);
        }
        // '首页'
        return view('static_pages/home', compact('feed_items'));
    }

    public function help()
    {
        // '帮助页'
        return view('static_pages/help');
    }

    public function about()
    {
        // '关于页'
        return view('static_pages/about');
    }
}
