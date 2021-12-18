<?php

namespace App\Http\Controllers\API;

use App;
use App\Http\Controllers\Controller;
use Backpack\PageManager\app\Models\Page;

class PageController extends Controller
{
    public function view($url){
        if (\Cache::has('page_' . $url)){
            return \Response::json([
                'page' => \Cache::get('page_' . $url)
            ]);
        }else{
            $page = Page::where('slug', $url)->first();

            if($page){
                \Cache::set('page_' . $url, $page, 60 * 60 * 30);

                return \Response::json([
                    'page' => $page
                ]);
            }

            App::abort(404, 'Такая страница не существует.');
        }
    }
}
