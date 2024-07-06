<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class ImportSql extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'db:import {file}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import an SQL file into the database';

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $file = $this->argument('file');

        if (!File::exists($file)) {
            $this->error("File not found: $file");
            return;
        }

        $sql = File::get($file);

        try {
            DB::unprepared($sql);
            $this->info('Database import successful.');
        } catch (\Exception $e) {
            $this->error("Error occurred: " . $e->getMessage());
        }
    }
}
