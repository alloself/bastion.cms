<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class LinkStorage extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'storage:public:link';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

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
        if (file_exists(public_path('public'))) {
            return $this->error('The "public/public" directory already exists.');
        }

        $this->laravel->make('files')->link(
            storage_path('app/public'),
            public_path('public')
        );

        $this->info('The [public/public] directory has been linked.');
    }
}
