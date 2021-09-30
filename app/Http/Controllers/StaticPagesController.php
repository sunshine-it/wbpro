<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class StaticPagesController extends Controller
{
    public function home()
    {
        // '首页'
        return view('static_pages/home');
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
