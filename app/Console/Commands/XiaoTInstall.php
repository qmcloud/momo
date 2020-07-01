<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class XiaoTInstall extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'XiaoT:install';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Install SS Live';

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
        $this->call('admin:install');
        $this->call('migrate');
        $this->call('db:seed');
        $this->call('passport:install');
    }
}
