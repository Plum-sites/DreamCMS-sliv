<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Telegram\Bot\Laravel\Facades\Telegram;

class WebController extends Controller
{
    public function layout(Request $request){
        return view('layouts.app');
    }

    public function telegram(Request $request){
        Telegram::commandsHandler(true);
    }
}