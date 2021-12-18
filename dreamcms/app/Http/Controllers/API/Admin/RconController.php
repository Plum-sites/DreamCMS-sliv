<?php

namespace App\Http\Controllers\API\Admin;

use App\Http\Controllers\Controller;
use App\Models\Server;
use Gate;
use Illuminate\Http\Request;
use Response;

class RconController extends Controller{
    public function __construct()
    {
        $this->middleware(function ($request, $next)
        {
            if (Gate::denies('ext', ['admin', 'rcon.access'])){
                abort(403, 'Недостаточно прав!');
            }

            return $next($request);
        });
    }

    public function sendCommand(Request $request){
        $servers = [];
        foreach ($request->get('servers') as $server){
            $servers[] = $server = Server::findOrFail($server);

            if (Gate::denies('ext', [$server, 'rcon'])){
                abort(403, 'Недостаточно прав!');
            }
        }

        $cmd = $request->get('cmd');

        $failed = 0;

        $responses = [];

        /* @var Server $server */
        foreach ($servers as $server){
            try{
                $response = $server->sendCommand($cmd);
                if (!$response){
                    $failed++;
                }else{
                    $responses[$server->id] = minecraft_string($response);
                }
            }catch (\Exception $ex){
                $failed++;
            }
        }

        if(count($servers) > $failed) {
            return Response::json([
                'success' => true,
                'failed'  => $failed,
                'messages' => $responses,
            ]);
        }else{
            return Response::json([
                'success' => false,
                'message' => 'Не удалось выполнить команду',
            ]);
        }
    }

    public function listPlayers(Request $request){
        $server = Server::findOrFail($request->get('server'));
    
        if (Gate::denies('ext', [$server, 'rcon'])){
            abort(403, 'Недостаточно прав!');
        }
        
        $responce = [];

        return Response::json([
            'players' => $responce
        ]);
    }
}