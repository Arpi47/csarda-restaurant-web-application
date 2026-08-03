<?php

return [

    'calendar_id' => env('GOOGLE_CALENDAR_ID'),

    'serbian_holidays_calendar_id' => env(
        'GOOGLE_SERBIAN_HOLIDAYS_CALENDAR_ID'
    ),

    'hungarian_holidays_calendar_id' => env(
        'GOOGLE_HUNGARIAN_HOLIDAYS_CALENDAR_ID'
    ),

    'credentials' => base_path(
        env(
            'GOOGLE_CALENDAR_CREDENTIALS',
            'storage/app/google/calendar-service-account.json'
        )
    ),

];
