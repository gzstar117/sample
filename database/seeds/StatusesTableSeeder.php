<?php

use Illuminate\Database\Seeder;
use App\Models\Status;
use App\Models\User;

class StatusesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $user_id = ['1','2','3'];
        $faker = app(Faker\Generator::class);
        $statuses = factory(Status::class)->times(100)->make()->each(function ($status) use($user_id, $faker) {
            $status->user_id = $faker->randomElement($user_id);
        });

        Status::insert($statuses->toArray());

    }
}
