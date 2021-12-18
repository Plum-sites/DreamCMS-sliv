<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Lib\Skinlib;
use Artisan;
use Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Image;
use Response;
use Storage;

class SkinController extends Controller {

    protected $SIZES = [
        'skin' => [
            '64x32' => 0,
            '64x64' => 0,
            '128x64' => 1,
            '256x128' => 1,
            '512x256' => 1,
            '1024x512' => 1
        ],
        'cloak' => [
            '22x17' => 0,
            '64x32' => 1,
            '64x64' => 1,
            '128x64' => 1,
            '256x128' => 1,
            '512x256' => 1,
            '1024x512' => 1
        ]
    ];
    public function __construct(){
        require_once app_path('Libraries' . DIRECTORY_SEPARATOR .'Skinlib.php');
    }

    public function delete(Request $request){
        $type = $request->get('type');

        $heads_storage = Storage::disk('heads');

        $filename = Auth::user()->uuid . '.png';

        if ($type == 'cloak'){
            $storage = Storage::disk('cloaks');
            $storage->delete($filename);

            $this->clearCache('cloaks/' . $filename);
        }elseif ($type == 'skin'){
            $storage = Storage::disk('skins');
            $storage->delete($filename);
            $heads_storage->delete($filename);

            $this->clearCache('heads/' . $filename);
            $this->clearCache('skins/' . $filename);
        }else{
            return Response::json(array(
                'success' => false,
                'message' => 'Ошибка удаления! Пожалуйста, выберите тип файла!'
            ));
        }

        return Response::json(array(
            'success' => true,
            'message' => 'Вы успешно удалили свой '. ($type == 'skin' ? 'скин' : 'плащ') .'!'
        ));
    }

    public function clearCache($path){
        try{
            if ($CDN_URL = config('cloudflare.cdn_url')){
                Artisan::call('cloudflare:cache:purge --file=' . $CDN_URL . $path);
            }
        }catch (\Throwable $exception){}
    }

    public function upload(Request $request){
        $type = $request->get('type');
        $file = $request->file('file');

        if ($type == 'cloak'){
            $storage = Storage::disk('cloaks');
        }elseif ($type == 'skin'){
            $storage = Storage::disk('skins');
        }else{
            return Response::json(array(
                'success' => false,
                'message' => 'Ошибка загрузки! Пожалуйста, выберите тип файла!'
            ));
        }

        if(!Auth::user()->hasPermissionTo('upload.' . $type . '.edit')){
            return Response::json(array(
                'success' => false,
                'message' => 'У вас нет права загружать '. ($type == 'skin' ? 'скины' : 'плащи') .'!'
            ));
        }

        if ($file) {
            if ($file->getMimeType() == 'image/png'){
                $image = Image::make($file->getRealPath());

                if(!in_array($image->width() . 'x' . $image->height(), array_keys($this->SIZES[$type]))){
                    return Response::json(array(
                        'success' => false,
                        'message' => 'Неверный размер картинки!'
                    ));
                }

                if($this->SIZES[$type][$image->width() . 'x' . $image->height()]){
                    if(!Auth::user()->hasPermissionTo('upload.' . $type . '.hd.edit')){
                        return Response::json(array(
                            'success' => false,
                            'message' => 'У вас нет права загружать HD скины!'
                        ));
                    }
                }

                $filename = Auth::user()->uuid . '.png';

                $image = $image->encode('png');

                $storage->delete($filename);
                $storage->put($filename, $image);

                //Generate head
                if ($type == 'skin'){
                    try{
                        $head = Skinlib::createHeadImage($image, 64);

                        $heads_storage = Storage::disk('heads');
                        $heads_storage->delete($filename);
                        $heads_storage->put($filename, $head->encode('png'));
                    }catch(\Throwable $exception){
                        Log::error($exception->getMessage());
                    }
                }

                $this->clearCache('skins/' . $filename);
                $this->clearCache('heads/' . $filename);
                $this->clearCache('cloaks/' . $filename);

                return Response::json(array(
                    'success' => true,
                    'message' => 'Вы успешно обновили вид персонажа! Ваш скин обновится в течении 10 минут!'
                ));
            } else return Response::json(array(
                'success' => false,
                'message' => 'Ошибка загрузки! Пожалуйста, выберите PNG файл!'
            ));
        }

        return Response::json(array(
            'success' => false,
            'message' => 'Ошибка загрузки файла! Проверьте интернет соединение!'
        ));
    }
}