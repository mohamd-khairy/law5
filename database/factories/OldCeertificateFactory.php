<?php
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
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

$factory->define(App\Model\OldCertificate::class, function (Faker\Generator $faker) {
    return [

        'id' => Str::random(10),
        'certificateTypeId' => $faker->numberBetween(1, 2),
        'companyName' => $faker->name,
        'companyActivity' => $faker->text,
        'productName' => $faker->name,
        'copy' => $faker->randomDigitNotNull,
        'companyAddress' => $faker->address,
        'companyCity' => $faker->city,
        'companyRegNo' => $faker->randomDigitNotNull,
        'localPercentage' => $faker->randomFloat,
        'manufacturingByOthers' => $faker->boolean,
        'manufacturingCompanyName' => $faker->name,
        'manufacturingCompanyIndustrialRegistry' => $faker->name,
        'issueYear' => $faker->numberBetween(2000, 2019) ,
        'startDate' => $faker->dateTime,
        'endDate' => $faker->dateTime,
    ];
});
