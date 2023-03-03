<?php

use App\Class\People;
use App\Class\PeopleList;

require __DIR__ . '/vendor/autoload.php';

$human = new People(
    gender: 0,
    firstName: 'Nikita',
    lastName: 'Tititkin',
    birthPlace: 'Grodno',
    birthDate: '23-04-1999',
);

$arrayPeople = new PeopleList(['gender' => 1], '=');
