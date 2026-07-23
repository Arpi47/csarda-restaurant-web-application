<?php

return [

    'required' => 'Поље :attribute је обавезно.',

    'email' => 'Поље :attribute мора бити исправна email адреса.',

    'confirmed' => 'Потврда за :attribute се не поклапа.',

    'min' => [
        'string' => ':attribute мора имати најмање :min карактера.',
        'numeric' => ':attribute мора бити најмање :min.',
    ],

    'max' => [
        'string' => ':attribute не може имати више од :max карактера.',
        'numeric' => ':attribute не може бити већи од :max.',
    ],

    'integer' => ':attribute мора бити цео број.',

    'string' => ':attribute мора бити текст.',

    'date' => ':attribute мора бити исправан датум.',

    'after_or_equal' => ':attribute мора бити после или једнак :date.',

    'unique' => ':attribute је већ заузет.',

    'regex' => ':attribute формат није исправан.',

    'in' => 'Изабрани :attribute није исправан.',

    'password' => [

        'mixed' => ':attribute мора садржати велика и мала слова.',

        'numbers' => ':attribute мора садржати најмање један број.',

        'symbols' => ':attribute мора садржати најмање један специјални знак.',

        'uncompromised' => 'Ова :attribute се налази у цурењу података.',

    ],

    'attributes' => [

        'first_name' => 'име',

        'last_name' => 'презиме',

        'email' => 'email',

        'password' => 'лозинка',

        'password_confirmation' => 'потврда лозинке',

        'guests' => 'број гостију',

        'date' => 'датум',

        'time' => 'време',

    ],

];
