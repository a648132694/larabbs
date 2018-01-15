<?php

use Faker\Generator as Faker;

$factory->define(App\Models\Reply::class, function (Faker $faker) {

    $createTime = $faker->dateTimeThisMonth();
    $updateTime = $faker->dateTimeThisMonth($createTime);
    return [
        // 'name' => $faker->name,
        'content'=>$faker->sentence(),
        'created_at' => $createTime,
        'updated_at' => $updateTime,
    ];
});
