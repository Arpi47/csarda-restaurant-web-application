<?php

namespace App\Console\Commands;

use App\Services\GoogleCalendarOpeningHoursSyncService;
use Illuminate\Console\Command;

class SyncGoogleCalendarOpeningHours extends Command
{
    protected $signature = 'google-calendar:sync-opening-hours';

    protected $description = 'Sync special opening hours from Google Calendar';

    public function handle(
        GoogleCalendarOpeningHoursSyncService $syncService
    ): int {
        $this->info(
            'Synchronizing Google Calendar opening hours...'
        );

        try {
            $syncedCount = $syncService->sync();

            $this->info(
                'Synchronization completed successfully.'
            );

            $this->info(
                'New Google Calendar opening hours synced: '
                . $syncedCount
            );

            return self::SUCCESS;

        } catch (\Throwable $exception) {
            $this->error(
                'Google Calendar synchronization failed.'
            );

            $this->error(
                $exception->getMessage()
            );

            return self::FAILURE;
        }
    }
}
