<?php

return [

    'required' => 'Polje :attribute je obavezno.',

    'email' => 'Polje :attribute mora biti ispravna email adresa.',

    'confirmed' => 'Potvrda za :attribute se ne poklapa.',

    'min' => [
        'string' => ':attribute mora imati najmanje :min karaktera.',
        'numeric' => ':attribute mora biti najmanje :min.',
    ],

    'max' => [
        'string' => ':attribute ne može imati više od :max karaktera.',
        'numeric' => ':attribute ne može biti veći od :max.',
    ],

    'integer' => ':attribute mora biti ceo broj.',

    'string' => ':attribute mora biti tekst.',

    'date' => ':attribute mora biti ispravan datum.',

    'after_or_equal' => ':attribute mora biti nakon ili jednak :date.',

    'unique' => ':attribute je već zauzet.',

    'regex' => ':attribute format nije ispravan.',

    'in' => 'Izabrani :attribute nije ispravan.',

    'password' => [

        'mixed' => ':attribute mora sadržati velika i mala slova.',

        'numbers' => ':attribute mora sadržati najmanje jedan broj.',

        'symbols' => ':attribute mora sadržati najmanje jedan specijalni znak.',

        'uncompromised' => 'Ovaj :attribute se nalazi u curenju podataka.',

    ],

    'attributes' => [

        'first_name' => 'ime',

        'last_name' => 'prezime',

        'email' => 'email',

        'password' => 'lozinka',

        'password_confirmation' => 'potvrda lozinke',

        'guests' => 'broj gostiju',

        'date' => 'datum',

        'time' => 'vreme',

    ],

];
