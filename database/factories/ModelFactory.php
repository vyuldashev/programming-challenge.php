<?php

/*
|--------------------------------------------------------------------------
| Model Factories
|--------------------------------------------------------------------------
|
| Here you may define all of your model factories. Model factories give
| you a convenient way to create models for testing and seeding your
| database. Just tell the factory how a default model should look.
|
*/

/** @var Illuminate\Database\Eloquent\Factory $factory */

use Carbon\Carbon;

$factory->define(App\Models\Task::class, static function (Faker\Generator $faker) {
    return [
        'title' => $faker->words(random_int(1, 5), true),
        'remind_at' => Carbon::now()->addMinutes(random_int(1, Carbon::HOURS_PER_DAY * Carbon::MINUTES_PER_HOUR)),
    ];
});
