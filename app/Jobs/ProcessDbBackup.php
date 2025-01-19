<?php

namespace App\Jobs;

use App\Models\User;
use Illuminate\Support\Str;
use App\Concerns\BackupDatabase;
use App\Models\Database_backups;
use App\Events\DatabaseBackupEvent;
use Illuminate\Support\Facades\Log;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\Notifications\DatabaseBackupNotification;

class ProcessDbBackup implements ShouldQueue
{
    use Queueable, BackupDatabase;

    public $timeout =300; // 5minutes

    /**
     * Create a new job instance.
     */
    public function __construct(public int $user_id)
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {

        $user = User::find($this->user_id);
        $user_id = $user->id;

        try {
            // Get Database credentials
            // $database = config('database.connections.mysql.database');
            // $username = config('database.connections.mysql.username');
            // $password = config('database.connections.mysql.password');
            // $host = config('database.connections.mysql.host');
            // $port = config('database.connections.mysql.port');

            // Base file name (used for both SQL and ZIP files)
            $base_file_name = 'u723498937_web_prhism' . '_backup_' . Str::random(8);

            // SQL file path
            $sql_file_name = $base_file_name . '.sql';
            $sql_file_path = str_replace('\\', '/', storage_path("app/backups/$sql_file_name"));

            // Create the backup using mysqldump command
            $backup_cmd = "mysqldump --user=u723498937_paombong --password=Prhism@rhu2024 --host=127.0.0.1 --port=3306 --single-transaction --quick --lock-tables=false u723498937_web_prhism > \"$sql_file_path\"";

            exec($backup_cmd, $output, $result);
            Log::info("Backup output for user $user_id: " . implode("\n", $output));

            if ($result) {
                Log::error("Backup failed for user $user_id: " . implode("\n", $output));
                throw new \Exception('Database backup failed.');
            }

            // ZIP file path
            $zip_file_name = $base_file_name . '.zip';
            $zip_file_path = storage_path("app/backups/$zip_file_name");

            $zip = new \ZipArchive();
            if ($zip->open($zip_file_path, \ZipArchive::CREATE) === true) {
                $zip->addFile($sql_file_path, basename($sql_file_path));
                $zip->close();
            } else {
                throw new \Exception('Failed to create ZIP file.');
            }

            // Delete the original SQL file
            unlink($sql_file_path);

            // Save backup record to the database
            Database_backups::create(['path' => $zip_file_name, 'user_id' => $user_id]);

            $user ->notify(new DatabaseBackupNotification($user));
            $notification = $user->notifications()->latest()->first();
            broadcast(new DatabaseBackupEvent($notification,$user));

        } catch (\Exception $e) {
            // Handle or rethrow the exception
            Log::error("Backup process failed for user $user_id: " . $e->getMessage());
            throw $e;
        }


    }
}
