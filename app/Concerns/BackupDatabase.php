<?php

namespace App\Concerns;

use App\Models\Database_backup;
use App\Models\Database_backups;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

trait BackupDatabase
{
    protected function performBackup(int $user_id)
{
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

        return $zip_file_name;

    } catch (\Exception $e) {
        // Handle or rethrow the exception
        Log::error("Backup process failed for user $user_id: " . $e->getMessage());
        throw $e;
    }
}

protected function restoreBackup(string $backupFilePath, int $user_id)
{
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

        return true;

    } catch (\Exception $e) {
        // Log the error and rethrow the exception
        Log::error("Restore process failed for user $user_id: " . $e->getMessage());
        throw $e;
    }
}




}
