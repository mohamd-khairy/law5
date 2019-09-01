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

$factory->define(App\User::class, function (Faker\Generator $faker) {
    return [
        'name' => $faker->name,
        'email' => $faker->email,
    ];
});

/**
 * Factory definition for model App\AssessmentMethod.
 */
/* $factory->define(App\AssessmentMethod::class, function ($faker) {
    return [
        // Fields here
    ];
}); */

/**
 * Factory definition for model App\Chamber.
 */
$factory->define(App\Chamber::class, function ($faker) {
    return [
        // Fields here
    ];
});

/**
 * Factory definition for model App\Chamber.
 */
$factory->define(App\Chamber::class, function ($faker) {
    return [
        // Fields here
    ];
});

/**
 * Factory definition for model App\Chamber.
 */
$factory->define(App\Chamber::class, function ($faker) {
    return [
        // Fields here
    ];
});

/**
 * Factory definition for model App\Sector.
 */
$factory->define(App\Sector::class, function ($faker) {
    return [
        // Fields here
    ];
});

/**
 * Factory definition for model App\Role.
 */
$factory->define(App\Role::class, function ($faker) {
    return [
        // Fields here
    ];
});
