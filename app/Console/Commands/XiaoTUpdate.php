<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class XiaoTUpdate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'XiaoT:update';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update SS Live';

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
        $bar = $this->output->createProgressBar(100);
        $this->call('migrate');
        usleep(2000);
        $bar->advance(20);
        $this->call('db:seed',['--class'=>'VersionTableSeeder']);
        usleep(600);
        $bar->advance(10);
        $version = DB::table('version')->where('status', '0')->orderBy('id', 'asc')->get();
        if($version->isEmpty()){
            $bar->finish();
            return false;
        }
        foreach ($version as $key => $value) {
            if($value->seed){
                $seeds = explode(',', $value->seed);
                foreach ($seeds as $seeds_key => $seeds_value) {
                    if($seeds_value){
                        $tmp_seed = ['--class'=>trim($seeds_value)];
                        $this->call('db:seed',$tmp_seed);
                        $bar->advance(2);
                    }
                }
            }
        }
        DB::table('version')->where('status', '0')->update(['status' => 1]);
        $bar->finish();
    }
}
