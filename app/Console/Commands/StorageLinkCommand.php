<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class StorageLinkCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'storage:link';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'create a symbolic link from "public/storage/ to "storage/app/public" ';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        if(\file_exists(base_path('public/storage'))){
            return $this->error('the "public/storage" directory aleardy exists.');
        }
        $this->laravel->make('files')->link(
            storage_path('app/public'),base_path('public/storage')
        );
        $this->info('the [public/storage] directory has been linked .');
    }
}
