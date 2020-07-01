<?php

namespace App\Console\Commands;

use App\Models\Order;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class AutoCompleteOrder extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'order:autocomplete';

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
        //
        Carbon::setToStringFormat('Y-m-d');

        $inSchoolTime = Carbon::now()->subDays(5);
        $outSchoolTime = Carbon::now()->subDays(10);
        DB::table('order')
            ->where([
                ['status', '=', Order::STATUS_DELIVERING],
                ['sid', '<>', 0 ],
            ])
            ->whereDate('updated_at', '<=', $inSchoolTime)
            ->update(['status' => Order::STATUS_COMPLETED]);

        DB::table('order')
            ->where([
                ['status', '=', Order::STATUS_DELIVERING],
                ['sid', '=', 0 ],
            ])
            ->whereDate('updated_at', '<=', $outSchoolTime)
            ->update(['status' => Order::STATUS_COMPLETED]);
    }
}
