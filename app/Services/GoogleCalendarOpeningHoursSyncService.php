<?php

namespace App\Services;

use App\Models\SpecialOpeningHour;
use Carbon\Carbon;

class GoogleCalendarOpeningHoursSyncService
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
        $events = $this->googleCalendarService->getEvents(
            $timeMin->toRfc3339String(),
            $timeMax->toRfc3339String()
        );
        $syncedCount = 0;
        $googleCalendarEventIds = [];
        foreach ($events as $event) {
            $summary = trim(
                (string) $event->getSummary()
            );
            if ($summary === '') {
                continue;
            }
            $googleCalendarEventId = $event->getId();
            if (!$googleCalendarEventId) {
                continue;
            }
            $googleCalendarEventIds[] =
                $googleCalendarEventId;
            $start = $event->getStart();
            $eventDate = $start->getDate();
            if (!$eventDate) {
                $dateTime = $start->getDateTime();
                if (!$dateTime) {
                    continue;
                }
                $eventDate = Carbon::parse(
                    $dateTime
                )->toDateString();
            }
            $summaryLower = mb_strtolower(
                $summary
            );
            $type = match (true) {
                str_contains(
                    $summaryLower,
                    'kitchen'
                ) => 'kitchen',
                str_contains(
                    $summaryLower,
                    'restaurant'
                ) => 'restaurant',
                default => 'restaurant',
            };
            $isClosed = str_contains(
                $summaryLower,
                'closed'
            );
            $openingHour = SpecialOpeningHour::firstOrNew([
                'google_calendar_event_id' =>
                    $googleCalendarEventId,
            ]);
            if (
                $openingHour->exists &&
                $openingHour->is_manually_deleted
            ) {
                continue;
            }
            if (
                $openingHour->exists &&
                $openingHour->is_manually_overridden
            ) {
                continue;
            }
            $openingHour->type = $type;
            $openingHour->date = $eventDate;
            $openingHour->is_active = !$isClosed;
            $openingHour->is_google_calendar = true;
            $openingHour->is_manually_deleted = false;
            if (!$openingHour->exists) {
                $openingHour->open_time = null;
                $openingHour->close_time = null;
                $openingHour->last_reservation_time = null;
                $openingHour->is_manually_overridden = false;
            }
            $openingHour->save();
            $syncedCount++;
        }
        SpecialOpeningHour::where(
            'is_google_calendar',
            true
        )
            ->whereBetween(
                'date',
                [
                    $timeMin->toDateString(),
                    $timeMax->toDateString(),
                ]
            )
            ->where('is_manually_deleted', false)
            ->where('is_manually_overridden', false)
            ->whereNotIn(
                'google_calendar_event_id',
                $googleCalendarEventIds
            )
            ->delete();
        return $syncedCount;
    }
}
