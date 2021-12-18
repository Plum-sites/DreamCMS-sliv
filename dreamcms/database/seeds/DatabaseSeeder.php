<?php

use App\Models\Shop;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Permission;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $faker = \Faker\Factory::create();
        $now = \Carbon\Carbon::now();

        try {
            $admin = User::create([
                'id'             => 1,
                'login'          => 'Admin',
                'uuid'           => $faker->unique()->uuid,
                'email'          => config('mail.from.address'),
                'password'       => bcrypt('secret'),
                'remember_token' => Str::random(10),
                'reg_time'       => $now->getTimestamp(),
                'passchange_time'=> $now->getTimestamp(),
                'reg_ip'         => $faker->unique()->ipv4,
                'realmoney'      => 1000000,
                'money'          => 0
            ]);

            $admin->roles()->attach(1);
            $admin->givePermissionTo(Permission::all());
        }catch (Throwable $exception){}

        try {
            \App\Models\Server::create([
                'sort' => 1,
                'name' => 'TestServer',
                'version' => '1.7.10',
                'icon' => '/uploads/servers/TestServer.png',
                'active' => 1,
                'donate' => 1,
                'ip' => '127.0.0.1',
                'port' => 25565,
                'rcon_port' => 25566,
                'rcon_password' => 'secret',
                'online' => 0,
                'maxonline' => 100,
                'db_host' => '127.0.0.1',
                'db_name' => 'server',
                'db_user' => 'homestead',
                'db_pass' => 'secret',
                'pexmanager' => '',
                'ecomanager' => '',
                'api_token' => $faker->unique()->uuid,
                'shop_id' => Shop::all()->first->id,
                'branch' => 'TestServer'
            ]);
        }catch (Throwable $exception){}


    }
}
