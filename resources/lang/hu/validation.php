<?php

return [

    'required' => 'A(z) :attribute mező megadása kötelező.',

    'email' => 'A(z) :attribute mezőnek érvényes e-mail címnek kell lennie.',

    'confirmed' => 'A(z) :attribute megerősítése nem egyezik.',

    'min' => [
        'string' => 'A(z) :attribute legalább :min karakter hosszú kell legyen.',
        'numeric' => 'A(z) :attribute értéke legalább :min kell legyen.',
    ],

    'max' => [
        'string' => 'A(z) :attribute maximum :max karakter lehet.',
        'numeric' => 'A(z) :attribute értéke maximum :max lehet.',
    ],

    'integer' => 'A(z) :attribute értékének egész számnak kell lennie.',

    'string' => 'A(z) :attribute szöveg kell legyen.',

    'date' => 'A(z) :attribute érvényes dátum kell legyen.',

    'after_or_equal' => 'A(z) :attribute nem lehet korábbi, mint :date.',

    'unique' => 'A(z) :attribute már használatban van.',

    'regex' => 'A(z) :attribute formátuma érvénytelen.',

    'in' => 'A kiválasztott :attribute érvénytelen.',


    'password' => [

        'mixed' => 'A(z) :attribute tartalmazzon kis- és nagybetűket.',

        'numbers' => 'A(z) :attribute tartalmazzon legalább egy számot.',

        'symbols' => 'A(z) :attribute tartalmazzon legalább egy speciális karaktert.',

        'uncompromised' => 'A megadott :attribute adatvédelmi szivárgásban szerepel.',

    ],


    'attributes' => [

        'first_name' => 'keresztnév',

        'last_name' => 'vezetéknév',

        'email' => 'e-mail cím',

        'password' => 'jelszó',

        'password_confirmation' => 'jelszó megerősítés',

        'guests' => 'vendégek száma',

        'date' => 'dátum',

        'time' => 'időpont',

    ],

];