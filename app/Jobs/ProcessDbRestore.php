<?php

namespace App\Jobs;

use App\Concerns\BackupDatabase;
use App\Events\DatabaseRestoreEvent;
use App\Models\User;
use App\Notifications\DatabaseRestoreNotification;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Log;

class ProcessDbRestore implements ShouldQueue
{
    use Queueable, BackupDatabase;

    protected $backupFilePath;
    protected $userId;

    public $timeout =300; // 5minutes

    /**
     * Create a new job instance.
     */
    public function __construct(string $backupFilePath, int $userId)
    {
        $this->backupFilePath = $backupFilePath;
        $this->userId = $userId;
    }

    /**
     * Execute the job.
     */
    public function handle()
    {
        $user = User::find($this->userId);
        $user_id = $user->id;
        $backupFilePath= $this->backupFilePath;

        try {
            // Get database credentials
            // $database = config('database.connections.mysql.database');
            // $username = config('database.connections.mysql.username');
            // $password = config('database.connections.mysql.password');
            // $host = config('database.connections.mysql.host');
            // $port = config('database.connections.mysql.port');

            // Define the SQL file path (extracted from ZIP)
            $sql_file_path = str_replace('\\', '/', storage_path('app/backups/' . pathinfo($backupFilePath, PATHINFO_FILENAME) . '.sql'));

            // Ensure the ZIP backup file exists
            if (!file_exists(storage_path("app/backups/$backupFilePath"))) {
                throw new \Exception("Backup file not found at path: " . storage_path("app/backups/$backupFilePath"));
            }

            // Extract the ZIP file to get the SQL file
            $zip = new \ZipArchive();
            if ($zip->open(storage_path("app/backups/$backupFilePath")) === true) {
                $zip->extractTo(storage_path('app/backups/'));
                $zip->close();

                // Ensure the SQL file exists after extraction
                if (!file_exists($sql_file_path)) {
                    throw new \Exception("Extracted SQL file not found at path: $sql_file_path");
                }
            } else {
                throw new \Exception('Failed to open the ZIP file.');
            }

            // Restore the database using the MySQL command
            $restore_cmd = "mysql --user=u723498937_paombong --password=Prhism@rhu2024 --host=127.0.0.1 --port=3306 u723498937_web_prhism < \"$sql_file_path\"";

            exec($restore_cmd, $output, $result);
            Log::info("Restore output for user $user_id: " . implode("\n", $output));

            if ($result) {
                Log::error("Restore failed for user $user_id: " . implode("\n", $output));
                throw new \Exception('Database restore failed.');
            }

            // Optionally delete the extracted SQL file after the restore
            unlink($sql_file_path);

            $user ->notify(new DatabaseRestoreNotification($user , $backupFilePath));
            $notification = $user->notifications()->latest()->first();
            broadcast(new DatabaseRestoreEvent($notification,$user , $this->backupFilePath));

        } catch (\Exception $e) {
            // Log the error and rethrow the exception
            Log::error("Restore process failed for user $user_id: " . $e->getMessage());
            throw $e;
        }

    }
}
