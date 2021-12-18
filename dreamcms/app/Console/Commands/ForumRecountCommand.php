<?php

namespace App\Console\Commands;

use App\Models\Forum\Category;
use App\Models\Server;
use App\Models\UserGroup;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;

class ForumRecountCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'forum:recount';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Recount post and discussions in all categories';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $categories = Category::all();

        foreach ($categories as $category){
            $category->discussions_count = $category->discussions()->count();
            $category->posts_count = $category->postsCount();

            $last_disc = $category->lastDiscussion()->first();
            if ($last_disc) $category->last_post = $last_disc->post()->first()->id;

            $category->save();
        }
    }
}
