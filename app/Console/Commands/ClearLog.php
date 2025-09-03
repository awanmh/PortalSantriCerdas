<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class ClearLog extends Command
{
    protected $signature = 'log:clear';
    protected $description = 'Clear the Laravel log file';

    public function handle()
    {
        $logPath = storage_path('logs/laravel.log');

        if (File::exists($logPath)) {
            File::put($logPath, '');
            $this->info('✅ Laravel log file has been cleared.');
        } else {
            $this->error('⚠️ Log file does not exist.');
        }
    }
}
