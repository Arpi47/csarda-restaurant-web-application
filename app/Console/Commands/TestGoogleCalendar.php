<?php

namespace App\Console\Commands;

use App\Services\GoogleCalendarService;
use Illuminate\Console\Command;

class TestGoogleCalendar extends Command
{
    protected $signature = 'google-calendar:test';

    protected $description = 'Test the connection to Google Calendar';

    public function handle(
        GoogleCalendarService $googleCalendarService
    ): int {
        $this->info(
            'Connecting to Google Calendar...'
        );

        try {
            $events = $googleCalendarService->getEvents(
                now()
                    ->startOfMonth()
                    ->toRfc3339String(),

                now()
                    ->addMonths(12)
                    ->endOfMonth()
                    ->toRfc3339String()
            );

            if (empty($events)) {
                $this->info(
                    'Connection successful. No events found.'
                );

                return self::SUCCESS;
            }

            $this->info(
                'Connection successful.'
            );

            $this->info(
                'Events found: ' . count($events)
            );

            foreach ($events as $event) {
                $start = $event
                    ->getStart()
                    ->getDateTime()
                    ?? $event
                        ->getStart()
                        ->getDate();

                $this->line(
                    sprintf(
                        '- %s | %s',
                        $start,
                        $event->getSummary()
                    )
                );
            }

            return self::SUCCESS;

        } catch (\Throwable $exception) {
            $this->error(
                'Google Calendar connection failed.'
            );

            $this->error(
                $exception->getMessage()
            );

            return self::FAILURE;
        }
    }
}