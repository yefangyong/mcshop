<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use Faker\Generator as Faker;
use Illuminate\Support\Str;

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

$factory->define(\App\Models\User\User::class, function (Faker $faker) {
    return [
        'username' => $faker->name,
        'password' => \Illuminate\Support\Facades\Hash::make(123456), // password
        'gender'   => $faker->randomKey([0, 1]),
        'mobile'   => $faker->phoneNumber,
        'avatar'   => $faker->imageUrl()
    ];
});

$factory->define(\App\Models\User\Address::class, function (Faker $faker) {
    return [
        'user_id'        => 0,
        'province'       => '安徽省',
        'city'           => '六安市',
        'county'        => '舒城县',
        'address_detail' => $faker->streetAddress,
        'area_code'      => '',
        'postal_code'    => $faker->postcode,
        'tel'            => $faker->phoneNumber,
        'is_default'     => 0
    ];
});

$factory->state(\App\Models\User\User::class, 'address_default', function () {
    return [];
})->afterCreatingState(\App\Models\User\User::class, 'address_default', function ($user) {
    factory(\App\Models\User\Address::class)->create([
        'user_id'    => $user->id,
        'is_default' => 1
    ]);
});
