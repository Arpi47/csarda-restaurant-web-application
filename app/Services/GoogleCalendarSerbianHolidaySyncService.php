<?php

namespace App\Services;

use App\Models\SerbianHoliday;
use Carbon\Carbon;

class GoogleCalendarSerbianHolidaySyncService
{
    public function __construct(
        private GoogleCalendarService $googleCalendarService
    ) {
    }

    public function sync(
        ?Carbon $timeMin = null,
        ?Carbon $timeMax = null
    ): int {
        $timeMin ??= now()->startOfMonth();

        $timeMax ??= now()
            ->addMonths(12)
            ->endOfMonth();

        $events = $this->googleCalendarService
            ->getSerbianHolidayEvents(
                $timeMin->toRfc3339String(),
                $timeMax->toRfc3339String()
            );

        $googleEventIds = [];
        $syncedCount = 0;

        foreach ($events as $event) {
            $summary = trim(
                (string) $event->getSummary()
            );

            if ($summary === '') {
                continue;
            }

            $googleEventId = $event->getId();

            if (! $googleEventId) {
                continue;
            }

            $start = $event->getStart();

            $eventDate = $start->getDate();

            if (! $eventDate) {
                $dateTime = $start->getDateTime();

                if (! $dateTime) {
                    continue;
                }

                $eventDate = Carbon::parse(
                    $dateTime
                )->toDateString();
            }

            $googleEventIds[] = $googleEventId;

            $holiday = SerbianHoliday::where(
                'date',
                $eventDate
            )->first();

            if (! $holiday) {
                $holiday = new SerbianHoliday();

                $holiday->google_event_id =
                    $googleEventId;

                $holiday->restaurant_is_active = false;
                $holiday->restaurant_open_time = null;
                $holiday->restaurant_close_time = null;
                $holiday->restaurant_last_reservation_time = null;

                $holiday->kitchen_is_active = false;
                $holiday->kitchen_open_time = null;
                $holiday->kitchen_close_time = null;
                $holiday->kitchen_last_order_time = null;
            }

            $holiday->name = $summary;
            $holiday->date = $eventDate;

            $holiday->save();

            $syncedCount++;
        }

        SerbianHoliday::whereNotNull('google_event_id')
            ->when(
                ! empty($googleEventIds),
                function ($query) use ($googleEventIds) {
                    $query->whereNotIn(
                        'google_event_id',
                        $googleEventIds
                    );
                },
                function ($query) {
                    $query->whereNotNull(
                        'google_event_id'
                    );
                }
            )
            ->delete();

        return $syncedCount;
    }
}
