<?php

use App\Models\Contact;
use Faker\Generator as Faker;

$factory->define(Contact::class, function (Faker $faker) {
    return [
        'name' => $faker->name,
        'phoneNumber' => $faker->tollFreePhoneNumber,
        'user_id' => $faker->numberBetween($min = 1, $max = 2),
    ];
});
