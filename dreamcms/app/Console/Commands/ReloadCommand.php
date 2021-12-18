<?php

namespace App\Console\Commands;

use App\Http\Controllers\PaymentController;
use Illuminate\Console\Command;
use Cache;
use Symfony\Component\Console\Output\BufferedOutput;
use Telegram\Bot\Laravel\Facades\Telegram;

class ReloadCommand extends Command{

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'reload';

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
        opcache_reset();

        Cache::clear();

        $output = new BufferedOutput;

        \Artisan::call('cache:clear', [], $output);

        \Artisan::call('route:clear', [], $output);

        \Artisan::call('view:cache', [], $output);

        \Artisan::call('config:cache', [], $output);

        echo $output->fetch();

        Cache::clear();

        opcache_reset();
    }
}
