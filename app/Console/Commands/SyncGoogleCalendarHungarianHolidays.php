<?php

namespace App\Console\Commands;

use App\Services\GoogleCalendarHungarianHolidaySyncService;
use Illuminate\Console\Command;

class SyncGoogleCalendarHungarianHolidays extends Command
{
    protected $signature = 'google-calendar:sync-hungarian-holidays';
    protected $description = 'Sync Hungarian holidays from Google Calendar';
    public function handle(
        GoogleCalendarHungarianHolidaySyncService $syncService
    ): int {
        $this->info(
            'Synchronizing Hungarian holidays from Google Calendar...'
        );
        try {
            $syncedCount = $syncService->sync();
            $this->info(
                'Synchronization completed successfully.'
            );
            $this->info(
                'Hungarian holidays synchronized: '
                . $syncedCount
            );
            return self::SUCCESS;
        } catch (\Throwable $exception) {
            $this->error(
                'Google Calendar Hungarian holiday synchronization failed.'
            );
            $this->error(
                $exception->getMessage()
            );
            return self::FAILURE;
        }
    }
}