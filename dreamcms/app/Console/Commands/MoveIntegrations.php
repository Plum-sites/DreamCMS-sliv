<?php

namespace App\Console\Commands;

use App\Models\Integration;
use App\Models\User;
use Illuminate\Console\Command;

class MoveIntegrations extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'integrations:migrate';

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
     * @return int
     */
    public function handle()
    {
        $done = 0;

        /* @var $user User */
        foreach (User::whereNotNull('vk_id')->whereNotNull('vk_data')->get(['id', 'vk_data', 'vk_id']) as $user){
            try {
                $data = json_decode($user->vk_data);

                $new_data = [
                    'accessTokenResponseBody' => $data->accessTokenResponseBody,
                    'user_raw' => $data->user,
                    'avatar' => $data->avatar,
                    'email' => $data->email,
                    'user_id' => $data->id,
                    'nickname' => $data->nickname,
                    'name' => $data->name
                ];

                $integration = new Integration();
                $integration->user_id = $user->id;
                $integration->driver = 'vkontakte';
                $integration->ext_id = $data->id;
                $integration->data = json_encode($new_data);

                $integration->save();
                $done++;
            }catch (\Throwable $throwable){}
        }

        echo "Done " . $done . " users" . PHP_EOL;
    }
}
