<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class SystemCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'db:migrate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $dbName = "abdeen";
        $connection = mysqli_connect('localhost', 'root', '');
        if (!$connection) {
            $this->error('Failed to connect to MySQL: ' . mysqli_connect_error());
            return;
        }

        // Create the new database
        $query = "CREATE DATABASE IF NOT EXISTS $dbName";
        $result = mysqli_query($connection, $query);

        if (!$result) {
            $this->error('Failed to create database: ' . mysqli_error($connection));
            return;
        }
        mysqli_close($connection);

        $this->info('Start Migration System :  ');
        $this->info('--------------------------------------------------------------');
        Artisan::call('migrate --path=database/migrations/ --database=mysql');

        $this->info(Artisan::output());
        $this->call('db:seed');
        $this->call('db:import', ['file' => 'database/table_temp/permissions.sql']);
        $this->call('db:import', ['file' => 'database/table_temp/role__permissions.sql']);
        $this->call('db:import', ['file' => 'database/table_temp/world.sql']);
        $this->call('db:import', ['file' => 'database/table_temp/admins.sql']);
    }
}
