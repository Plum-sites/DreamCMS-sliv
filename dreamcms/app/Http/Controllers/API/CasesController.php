<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\CaseChest;
use App\Models\Server;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CasesController extends Controller
{
    public function open(Request $request){
        $server = Server::findOrFail($request->get('server'));
        $case = CaseChest::findOrFail($request->get('case'));

        if (Auth::user()->withdrawRealmoney($case->price)){
            return [
                'success' => true,
                'reward' => $case->openCase(Auth::user(), $server)
            ];
        }else{
            return [
                'success' => false,
                'message' => 'Недостаточно стримов!'
            ];
        }
    }
}