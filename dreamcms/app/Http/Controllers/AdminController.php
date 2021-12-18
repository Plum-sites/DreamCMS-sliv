<?php
namespace App\Http\Controllers;

class AdminController extends Controller
{
    public function layout(){
        return view('admin.vuexy');
    }
}