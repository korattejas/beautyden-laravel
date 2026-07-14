<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use ZipArchive;
use Illuminate\Support\Facades\File;

class EmailDatabaseBackup extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'backup:email-db {email}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Take a backup of the database, zip it, and email it to the specified address.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $email = $this->argument('email');
        $dbName = env('DB_DATABASE');
        $userName = env('DB_USERNAME');
        $password = env('DB_PASSWORD');
        $host = env('DB_HOST', '127.0.0.1');

        $date = now()->format('Y-m-d_H-i-s');
        $sqlPath = storage_path("app/backup_{$date}.sql");
        $zipPath = storage_path("app/backup_{$date}.zip");

        $this->info("Creating database dump...");
        try {
            $dsn = "mysql:host={$host};dbname={$dbName}";
            $dump = new \Ifsnop\Mysqldump\Mysqldump($dsn, $userName, $password);
            $dump->start($sqlPath);
        } catch (\Exception $e) {
            $this->error("Failed to dump database: " . $e->getMessage());
            return;
        }

        $this->info("Zipping the dump...");
        $zip = new ZipArchive;
        if ($zip->open($zipPath, ZipArchive::CREATE) === TRUE) {
            $zip->addFile($sqlPath, "database_backup_{$date}.sql");
            $zip->close();
        } else {
            $this->error("Failed to create zip file.");
            return;
        }

        $this->info("Sending email to {$email}...");
        try {
            Mail::raw("Hello,\n\nPlease find attached the daily database backup for your application.\n\nDate: " . now()->format('d M Y, h:i A') . "\n\nThanks,\nBeautyDen System", function ($msg) use ($email, $zipPath, $date) {
                $msg->to($email)
                    ->subject("Daily Database Backup - BeautyDen - " . $date)
                    ->attach($zipPath);
            });
            $this->info('Database backup emailed successfully.');
        } catch (\Exception $e) {
            $this->error("Failed to send email: " . $e->getMessage());
        }

        // Cleanup
        File::delete($sqlPath);
        File::delete($zipPath);
    }
}

