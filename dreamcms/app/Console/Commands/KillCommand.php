<?php

namespace App\Console\Commands;

use App\Models\Integration;
use App\Models\Server;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class KillCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'user:kill {login}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Remove expired bans';

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
        $login = $this->argument('login');

        $user = User::fromLogin($login);

        // Site roles

        $user->roles()->detach();
        $user->permissions()->detach();

        // ACL

        \DB::table('ext_permissions')->where([
            'permissible' => User::class,
            'permissible_id' => $user->id,
        ])->delete();

        $user->forgetCachedPermissions();

        // Server perms

        foreach (Server::getActive() as $server) {
            try {
                /* @var $server Server */

                $pm = $server->getPermissionManager();

                $pm->clearUser($user);
                $pm->clearUser($user->uuid);
            }catch (\Throwable $exception){
                echo "Не сняты права с " . $server->name . "!" . PHP_EOL;
            }
        }

        // Email * tokens

        $user->password = \Hash::make(Str::random(32));
        $user->email = Str::random(16) . "@localhost";
        $user->realmoney = 0;
        $user->money = 0;
        $user->prefix = null;
        $user->sign = null;
        $user->backup_codes = null;
        $user->accessToken = null;
        $user->remember_token = null;
        $user->token_reset = Carbon::now()->getTimestamp();

        $user->save();

        // Integrations

        foreach (Integration::where([
            'user_id' => $user->id,
        ])->get() as $integration){
            /* @var $integration Integration */
            echo "Найдена интеграция " . $integration->driver . " с ID " . $integration->ext_id;

            $integration->delete();
        }


        $user->clearCache();
    }
}
