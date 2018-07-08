<?php

use Illuminate\Database\Seeder;
use App\Models\User;

class FollowersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $users = User::all();   //获取所有用户
        $user = $users->first();//获取第一个用户
        $user_id= $user->id;    //获取第一个用户的ID

        $followers = $users->slice(1);      //获取除了第一个用户以外的用户
        $follower_ids = $followers->pluck('id')->toArray();     //获取除了第一个用户以外的用户的ID

        //将第一个用户去关注除了自己以外的用户
        $user->follow($follower_ids);

        //除了第一个用户之外的人都去关注第一个用户
        foreach ($followers as $follower) {
            $follower->follow($user_id);
        }
    }
}
