<?php

namespace App\Models;

use App\Models\Managers\FeEconomyManager;
use App\Models\Managers\LuckPermsManager;
use App\Models\Managers\MiniGamesEconomyManager;
use App\Models\Managers\MiniGamesPermissionManager;
use App\Models\Managers\PermissionEXManager;
use Backpack\CRUD\app\Models\Traits\CrudTrait;
use D3lph1\MinecraftRconManager\DefaultConnector;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use MinecraftServerStatus\MinecraftServerStatus;
use OwenIt\Auditing\Contracts\Auditable;

class Server extends \Eloquent implements Auditable
{
    use CrudTrait;
    use \OwenIt\Auditing\Auditable;

    protected $table = 'servers';

    public $timestamps = false;

    protected $fillable = [
        'sort', 'name', 'version', 'icon', 'active', 'donate', 'ip', 'port', 'rcon_port', 'rcon_password', 'online',
        'maxonline', 'db_host', 'db_name', 'db_user','db_pass', 'pexmanager', 'ecomanager', 'api_token', 'shop_id', 'branch'
    ];
    
    protected $hidden = [
        'rcon_password', 'db_pass', 'api_token', 'ip', 'port', 'rcon_port', 'db_host', 'db_name', 'db_user'
    ];

    protected $auditExclude = [
        'online',
        'maxonline',
    ];

    public function getStatus(){
        return MinecraftServerStatus::query($this->ip, $this->port);
    }

    public function shop(){
        return $this->hasOne('App\Models\Shop', 'id', 'shop_id');
    }

    public function sendCommand($command){
        $connector = new DefaultConnector();
        $rcon = $connector->connect($this->ip, $this->rcon_port, $this->rcon_password);
        return $rcon->send($command);
    }

    public function getEconomyManager(){
        $connect = Str::random(16) . '_feeconomy';
        Config::set('database.connections.' . $connect . '.driver', 'mysql');
        Config::set('database.connections.' . $connect . '.port', '3306');

        Config::set('database.connections.' . $connect . '.host', $this->db_host);
        Config::set('database.connections.' . $connect . '.database', $this->db_name);
        Config::set('database.connections.' . $connect . '.username', $this->db_user);
        Config::set('database.connections.' . $connect . '.password', $this->db_pass);

        Config::set('database.connections.' . $connect . '.charset', 'utf8');
        Config::set('database.connections.' . $connect . '.collation', 'utf8_unicode_ci');
        Config::set('database.connections.' . $connect . '.prefix', '');
        Config::set('database.connections.' . $connect . '.strict', false);

        if ($this->ecomanager == 'FE_ECONOMY'){
            return new FeEconomyManager(DB::connection($connect));
        }elseif ($this->name == 'MiniGames' || $this->ecomanager == 'MINIGAMES'){
            return new MiniGamesEconomyManager(DB::connection($connect));
        }
        return null;
    }

    public function getPermissionManager(){
        $connect = 'pexconnect_' . $this->id;
        
        Config::set('database.connections.' . $connect . '.driver', 'mysql');
        Config::set('database.connections.' . $connect . '.port', '3306');

        Config::set('database.connections.' . $connect . '.host', $this->db_host);
        Config::set('database.connections.' . $connect . '.database', $this->db_name);
        Config::set('database.connections.' . $connect . '.username', $this->db_user);
        Config::set('database.connections.' . $connect . '.password', $this->db_pass);

        Config::set('database.connections.' . $connect . '.charset', 'utf8');
        Config::set('database.connections.' . $connect . '.collation', 'utf8_unicode_ci');
        Config::set('database.connections.' . $connect . '.prefix', '');
        Config::set('database.connections.' . $connect . '.strict', false);

        Config::set('database.connections.' . $connect . '.options.2', 3);

        // Config::set('database.connections.' . $connect, [
        //     'driver' => 'mysql',
            
        //     'port' => 3306,
        //     'host' => $this->db_host,
        //     'database' => $this->db_name,
        //     'username' => $this->db_user,
        //     'password' => $this->db_pass,

        //     'charset' => 'utf8',
        //     'collation' => 'utf8_unicode_ci',
        //     'strict' => false,
        //     'prefix' => '',
        // ]);
        

        if ($this->pexmanager == 'PERMISSIONEX'){
            return new PermissionEXManager(DB::connection($connect));
        }elseif ($this->pexmanager == 'LUCKPERMS'){
            return new LuckPermsManager(DB::connection($connect));
        }elseif($this->name == 'MiniGames' || $this->pexmanager == 'MINIGAMES'){
            return new MiniGamesPermissionManager(DB::connection($connect));
        }
        return null;
    }

    /**
     * Return the collection of active donate groups.
     *
     * @return Collection
     */

    public static function getActive($get = ['*']){
        return Server::where('active', 1)->orderBy('sort')->get($get);
    }
}
