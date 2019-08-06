<?php

use Illuminate\Database\Seeder;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory(\App\User::class,50)->create()->each(function ($user) {
            $user->relationships()->create([
               'friend_id'  => \App\User::getUser()->id ?? 1,
               'status'     => RELATIONSHIP_STATUS_REQUESTED,
            ]);
        });;
    }
}
