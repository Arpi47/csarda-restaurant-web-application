<?php

namespace App\Console\Commands;

use App\Services\GoogleCalendarSerbianHolidaySyncService;
use Illuminate\Console\Command;

class SyncGoogleCalendarSerbianHolidays extends Command
{
    protected $signature = 'google-calendar:sync-serbian-holidays';

    protected $description = 'Sync Serbian holidays from Google Calendar';

    public function handle(
        GoogleCalendarSerbianHolidaySyncService $syncService
    ): int {
        $this->info(
            'Synchronizing Serbian holidays from Google Calendar...'
        );

        try {
            $syncedCount = $syncService->sync();

            $this->info(
                'Synchronization completed successfully.'
            );

            $this->info(
                'Serbian holidays synchronized: '
                . $syncedCount
            );

            return self::SUCCESS;
        } catch (\Throwable $exception) {
            $this->error(
                'Google Calendar Serbian holiday synchronization failed.'
            );

            $this->error(
                $exception->getMessage()
            );

            return self::FAILURE;
        }
    }
}