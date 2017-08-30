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

/** @var \Illuminate\Database\Eloquent\Factory $factory */
use Carbon\Carbon;

$factory->define(Lara\User::class, function (Faker\Generator $faker) {
    static $password;

    return [
        'name' => $faker->name,
        'email' => $faker->unique()->safeEmail,
        'password' => $password ?: $password = bcrypt('secret'),
        'remember_token' => str_random(10),
    ];
});

$factory->define(Lara\Person::class, function (Faker\Generator $faker) {
    return [
        'prsn_name' => $faker->name(),
        'prsn_ldap_id' => $faker->numberBetween(2000, 9999),
        'prsn_status' => $faker->randomElement(['member', 'veteran', 'candidate']),
        'prsn_uid' => hash("sha512", uniqid()),
        'clb_id' => $faker->randomElement([1,2, Lara\Club::inRandomOrder()->first()->id])
    ];
});


$factory->define(Lara\Survey::class, function (Faker\Generator $faker) {
    return [
        'creator_id' => Lara\Person::inRandomOrder()->first()->prsn_ldap_id,
        'title' => $faker->sentence(2),
        'description' => $faker->paragraphs(4, true),
        'description' => $faker->paragraphs(4, true),
        'deadline' => $faker->dateTimeBetween('now', '+60 days'),
        'password' => '',
        'is_private' => false,
        'is_anonymous' => false,
        'show_results_after_voting' => false
    ];
});

$factory->define(Lara\SurveyAnswer::class, function(Faker\Generator $faker){
    return [
        'creator_id' => Lara\Person::inRandomOrder()->first()->prsn_ldap_id,
        'survey_id' => Lara\Survey::inRandomOrder()->first()->id,
        'name' => $faker->firstName,
        'club' => $faker->word
    ];
});

$factory->define(Lara\SurveyAnswerOption::class, function(Faker\Generator $faker){
    return [
        'survey_question_id' => Lara\SurveyQuestion::inRandomOrder()->first()->id,
        'answer_option' => $faker->sentence(),
    ];
});

$factory->define(Lara\SurveyAnswerCell::class, function(Faker\Generator $faker){
    return [
        'survey_answer_id' => Lara\SurveyAnswer::inRandomOrder()->first()->id,
        'survey_question_id' => Lara\SurveyQuestion::inRandomOrder()->first()->id,
        'answer' => $faker->sentence,
    ];
});

$factory->define(Lara\SurveyQuestion::class, function(Faker\Generator $faker){
    return [
        'survey_id' => Lara\Survey::inRandomOrder()->first()->id,
        'order' => $faker->numberBetween(0, 5),
        'field_type' => 1,
        'question' => $faker->sentence,
        'is_required' => 0
    ];
});

$factory->define(Lara\ClubEvent::class, function(Faker\Generator $faker) {
    $start = $faker->dateTimeBetween('-30 days', '+60 days');
    $end = $faker->dateTimeBetween($start, date("Y-m-d H:i:s", strtotime('+1 day', $start->getTimestamp())));
    return [
        'evnt_type' => $faker->numberBetween(0,9),
        'evnt_title' => $faker->word(),
        'evnt_subtitle' => $faker->word(),
        'plc_id' => Lara\Section::inRandomOrder()->first()->id,
        'evnt_show_to_club' => json_encode($faker->randomElements(Lara\Section::all()->pluck('title')->toArray(), $faker->numberBetween(1,2))),
        'evnt_date_start' => $start->format('Y-m-d'),
        'evnt_date_end' => $end->format('Y-m-d'),
        'evnt_time_start' => $start->format('H:i'),
        'evnt_time_end' => $end->format('H:i'),
        'evnt_public_info' => $faker->sentence(),
        'evnt_private_details' => $faker->sentence(),
        'evnt_is_private' => $faker->boolean(10),
        'evnt_is_published' => 0
    ];
});

$factory->define(Lara\ShiftType::class, function(Faker\Generator $faker) {
    $types = ['Einlass', 'Bar', 'Tresen', 'AV', 'Disko', 'Licht'];
    $end = $faker->time('H:i');
    $start = $faker->time('H:i', $end);
    return [
        'title' => $faker->randomElement($types),
        'start' => $start,
        'end' => $end,
        'needs_preparation' => $faker->boolean(),
        'statistical_weight' => $faker->numberBetween(0, 4) * 0.5,
        'is_archived' => 0
    ];
});

$factory->define(Lara\Schedule::class, function(Faker\Generator $faker) {
    return [
        'schdl_title' => $faker->word(),
        'schdl_time_preparation_start' => $faker->time('H:i'),
        'schdl_is_template' => 0,
        'schdl_password' => '',
        'entry_revisions' => ''
    ];
});

$factory->define(Lara\Shift::class, function(Faker\Generator $faker) {
    $end = $faker->time('H:i');
    $start = $faker->time('H:i', $end);
    $personId = $faker->randomElement([Lara\Person::inRandomOrder()->first()->id, NULL]);
    return [
        'schedule_id' => Lara\Schedule::inRandomOrder()->first()->id,
        'shifttype_id' => Lara\ShiftType::inRandomOrder()->first()->id,
        'person_id' => $personId,
        'comment' => $personId ? $faker->randomElement([$faker->sentence, ""]) : "",
        'start' => $start,
        'end' => $end,
        'statistical_weight' => 1,
        'position' => 0
    ];
});

$factory->define(Lara\Club::class, function(Faker\Generator $faker) {
    return [
        'clb_title' => $faker->word(),
    ];
});
