<?php

namespace App\Console\Commands;

use App\Achievements\Achievement;
use App\Http\Controllers\PaymentController;
use App\Models\CaseChest;
use App\Models\Server;
use App\Models\User;
use Illuminate\Console\Command;

class TestCommand extends Command{

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:run';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Doesnt use it if you dont know what is doing!';

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
    public static function handle()
    {
        PaymentController::checkDigisellerOrders();
    }
}
