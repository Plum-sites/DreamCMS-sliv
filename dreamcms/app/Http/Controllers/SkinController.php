<?php

namespace App\Http\Controllers;

use App\Lib\Skinlib;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Image;
use Storage;

class SkinController extends Controller
{
    public function __construct(){
        require_once app_path('Libraries' . DIRECTORY_SEPARATOR .'Skinlib.php');
    }

    public function skin($uuid, $side = false, $size = 224){
        $filename = $uuid . '.png';

        try {
            $storage = Storage::disk('skins');
            $image_skin = Image::make($storage->get($filename));
        } catch (FileNotFoundException $e) {
            $image_skin = Image::make(public_path( 'skins' . DIRECTORY_SEPARATOR . 'default.png'));
        }

        try {
            $storage = Storage::disk('cloaks');
            $image_cloak = Image::make($storage->get($filename));
        } catch (FileNotFoundException $e) {
            $image_cloak = false;
        }

        try{
            $preview = Skinlib::createPreviewImage($image_skin, $image_cloak, $side, 224);

            //\Log::info(print_r($preview, true));

            return $preview->response('png');
        }catch (\Exception $exception){}
    }
}
