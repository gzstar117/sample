<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class Status extends Model
{
    protected $fillable = ['content'];

    //关联User模型
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
