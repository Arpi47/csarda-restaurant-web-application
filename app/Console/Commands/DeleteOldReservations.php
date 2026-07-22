<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Reservation;

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