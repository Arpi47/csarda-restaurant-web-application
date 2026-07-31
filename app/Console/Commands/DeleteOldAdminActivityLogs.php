<?php

namespace App\Console\Commands;

use App\Models\AdminActivityLog;
use Illuminate\Console\Command;

class DeleteOldAdminActivityLogs extends Command
{
    protected $signature = 'admin-activity:delete-old';

    protected $description = 'Delete admin activity logs older than 5 years';

    public function handle()
    {
        $deleted = AdminActivityLog::where(
            'created_at',
            '<',
            now()->subYears(5)
        )->delete();

        $this->info(
            "Deleted {$deleted} old admin activity log(s)."
        );

        return Command::SUCCESS;
    }
}