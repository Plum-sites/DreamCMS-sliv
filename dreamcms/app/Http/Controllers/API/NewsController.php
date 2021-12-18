<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Models\Comment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NewsController extends Controller
{
    public function index(Request $request){
        $page = $request->get('page');

        if (\Cache::has('news')){
            $posts = \Cache::get('news_' . $page);
        }else{
            $posts = Article::where('status', 'PUBLISHED')
                ->where('date', '<=', date('Y-m-d'))
                ->orderBy('date', 'DESC')->paginate(10);

            \Cache::set('news_' . $page, $posts, 10);
        }

        return \Response::json(['news' => $posts]);
    }

    public function view(Request $request, $url){
        $post = Article::getPublished()->where('slug', $url)->first();

        Article::disableAuditing();
        if (!$this->isPostViewed($post)){
            $post->views++;
            $post->save();
            $request->session()->push('viewed_posts', $post->id);
        }
        Article::enableAuditing();

        return \Response::json([
            'post' => $post,
            'comments' => Comment::whereArticleId($post->id)->with('user')
        ]);
    }

    private function isPostViewed($post)
    {
        /*$viewed = Session::get('viewed_posts', []);
        return in_array($post->id, $viewed);*/
        //TODO
        return true;
    }

    public function del_comment(Request $request, $post){
        if (Auth::user()->hasPermissionTo('forum.post.delete')){
            Comment::whereId($request->get('id'))->delete();
            return \Response::json([
                'success' => true,
                'message' => 'Комментарий удален!'
            ]);
        }
    }

    public function comment(Request $request, $post){
        $text = $request->input('text');
        $news = Article::where('id', $post)->get()->first();

        if(!Auth::user()->isBanned()){
            $request->session()->flash('alert-danger', "У вас нет прав оставлять комментарии!");
            return redirect()->route('home');
        }

        if ($news) {
            Comment::create([
                'article_id' => $post,
                'user_id' => Auth::id(),
                'text' => strip_tags($text)
            ]);

            return redirect()->route('news', $news->slug);
        }else{
            return redirect()->route('home');
        }
    }
}
