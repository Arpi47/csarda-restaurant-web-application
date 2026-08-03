<?php

namespace App\Services;

use Google\Client;
use Google\Service\Calendar;
use Google\Service\Calendar\Event;

class GoogleCalendarService
{
    private Calendar $calendar;

    public function __construct()
    {
        $client = new Client();
        $client->setApplicationName(
            config('app.name')
        );
        $client->setAuthConfig(
            config('google-calendar.credentials')
        );
        $client->addScope(
            Calendar::CALENDAR_READONLY
        );
        $this->calendar = new Calendar($client);
    }

    private function getCalendarId(): string
    {
        return config(
            'google-calendar.calendar_id'
        );
    }

    public function getEvents(
        ?string $timeMin = null,
        ?string $timeMax = null
    ): array {
        $params = [
            'singleEvents' => true,
            'orderBy' => 'startTime',
        ];
        if ($timeMin) {
            $params['timeMin'] = $timeMin;
        }
        if ($timeMax) {
            $params['timeMax'] = $timeMax;
        }
        $events = $this->calendar
            ->events
            ->listEvents(
                $this->getCalendarId(),
                $params
            );
        return $events->getItems();
    }

    public function getSerbianHolidayEvents(
        ?string $timeMin = null,
        ?string $timeMax = null
    ): array {
        $params = [
            'singleEvents' => true,
            'orderBy' => 'startTime',
        ];

        if ($timeMin) {
            $params['timeMin'] = $timeMin;
        }

        if ($timeMax) {
            $params['timeMax'] = $timeMax;
        }

        $events = $this->calendar
            ->events
            ->listEvents(
                config(
                    'google-calendar.serbian_holidays_calendar_id'
                ),
                $params
            );

        return $events->getItems();
    }

    public function getHungarianHolidayEvents(
        ?string $timeMin = null,
        ?string $timeMax = null
    ): array {
        $params = [
            'singleEvents' => true,
            'orderBy' => 'startTime',
        ];

        if ($timeMin) {
            $params['timeMin'] = $timeMin;
        }

        if ($timeMax) {
            $params['timeMax'] = $timeMax;
        }

        $events = $this->calendar
            ->events
            ->listEvents(
                config(
                    'google-calendar.hungarian_holidays_calendar_id'
                ),
                $params
            );

        return $events->getItems();
    }
}
