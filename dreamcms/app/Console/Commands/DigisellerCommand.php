<?php

namespace App\Console\Commands;

use App\Achievements\Achievement;
use App\Http\Controllers\PaymentController;
use App\Models\CaseChest;
use App\Models\Server;
use App\Models\User;
use Illuminate\Console\Command;

class DigisellerCommand extends Command{

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'digiseller:check';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check today digiseller orders';

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
