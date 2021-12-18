<?php
namespace App\Http\Controllers\Admin\Forum;

use App\Http\Controllers\Controller;

class ForumController extends Controller
{
    public function logs(){
        return view('admin.forum.logs');
    }
    
    public function permissions(){
        return view('admin.forum.permissions');
    }
}