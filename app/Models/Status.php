<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Status extends Model
{
    use HasFactory;

    // 允许更新微博的 content 字段
    protected $fillable = ['content'];

    public function user()
    {
        // 一条微博属于一个用户
        return $this->belongsTo(User::class);
    }
}
