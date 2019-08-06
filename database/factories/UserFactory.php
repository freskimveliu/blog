<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */
use App\User;
use Illuminate\Support\Str;
use Faker\Generator as Faker;

/*
|--------------------------------------------------------------------------
| Model Factories
|--------------------------------------------------------------------------
|
| This directory should contain each of the model factory definitions for
| your application. Factories provide a convenient way to generate new
| model instances for testing / seeding your application's database.
|
*/

$factory->define(User::class, function (Faker $faker) {
    $random_number = rand(1,1000);
    return [
        'name'                  => $faker->name,
        'email'                 => $faker->unique()->safeEmail,
        'username'              => $faker->slug,
        'bio'                   => $faker->realText(100),
        'image_url'             => "https://picsum.photos/id/$random_number/200/200",
        'email_verified_at'     => now(),
        'is_private_account'    => $faker->boolean,
        'password'              => bcrypt('freskimi'), // password
        'remember_token'        => Str::random(10),
    ];
});
