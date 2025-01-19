<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use App\Jobs\ProcessDbBackup;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class DatabaseBackup extends Command
{
    protected $signature = 'db:backup';
    protected $description = 'Backup the database';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        // Dispatch the job
        ProcessDbBackup::dispatch(1);
    }
}
