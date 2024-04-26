<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class BackupDatabase extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'backup:now';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This command is used to take bakcup in folder htdocs storage backups daily at 10 PM';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
       // Define backup path and filename
       $backupPath = storage_path('backups') . '/' . date('Y-m-d_His') . '.sql';

       // Construct the mysqldump command to create a database backup
       $command = "/opt/lampp/bin/mysqldump --user=root --password='' jovera > $backupPath";

       // Execute the mysqldump command
       exec($command);

       $this->info('Database backup completed successfully.');

       return 0;
    }
}
