<?php

return [

    'required' => 'The :attribute field is required.',

    'email' => 'The :attribute must be a valid email address.',

    'confirmed' => 'The :attribute confirmation does not match.',

    'min' => [
        'string' => 'The :attribute must be at least :min characters.',
        'numeric' => 'The :attribute must be at least :min.',
    ],

    'max' => [
        'string' => 'The :attribute may not be greater than :max characters.',
        'numeric' => 'The :attribute may not be greater than :max.',
    ],

    'integer' => 'The :attribute must be an integer.',

    'string' => 'The :attribute must be a string.',

    'date' => 'The :attribute must be a valid date.',

    'after_or_equal' => 'The :attribute must be a date after or equal to :date.',

    'unique' => 'The :attribute has already been taken.',

    'regex' => 'The :attribute format is invalid.',

    'in' => 'The selected :attribute is invalid.',

    'password' => [

        'mixed' => 'The :attribute must contain uppercase and lowercase letters.',

        'numbers' => 'The :attribute must contain at least one number.',

        'symbols' => 'The :attribute must contain at least one symbol.',

        'uncompromised' => 'The given :attribute has appeared in a data leak.',

    ],

    'attributes' => [

        'first_name' => 'first name',

        'last_name' => 'last name',

        'email' => 'email',

        'password' => 'password',

        'password_confirmation' => 'password confirmation',

        'guests' => 'number of guests',

        'date' => 'date',

        'time' => 'time',

    ],

];
