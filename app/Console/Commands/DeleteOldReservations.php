<?php

namespace App\Console\Commands;

use App\Models\Reservation;
use Illuminate\Console\Command;

class DeleteOldReservations extends Command
{
    protected $signature = 'reservations:delete-old';

    protected $description = 'Delete reservations older than 2 days';

    public function handle()
    {
        $deleted = Reservation::where(
            'date_time',
            '<',
            now()->subDays(2)
        )->delete();

        $this->info(
            "Deleted {$deleted} old reservation(s)."
        );

        return Command::SUCCESS;
    }
}
