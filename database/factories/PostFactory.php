<?php

/* @var $factory \Illuminate\Database\Eloquent\Factory */

use Faker\Generator as Faker;

$factory->define(\App\Models\Post::class, function (Faker $faker) {
    return [
        'title' => $faker->realText(90),
        'description' => $faker->realText(500),
        'category_id' => rand(1, 6),
        'user_id' => rand(1, 7),
    ];
});
