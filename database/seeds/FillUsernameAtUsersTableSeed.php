<?php

use Illuminate\Database\Seeder;

class FillUsernameAtUsersTableSeed extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $users = \App\User::whereNull('username')->get();
        foreach ($users as $user){
            $user->update([
                'username'  => \Illuminate\Support\Str::slug($user->name)
            ]);
        }
    }
}
