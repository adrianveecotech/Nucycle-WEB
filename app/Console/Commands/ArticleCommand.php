<?php

namespace App\Console\Commands;

use App\Helper\Helper;
use App\Models\Article;
use App\Models\Notification;
use App\Models\NotificationRecipient;
use App\Models\User;
use Illuminate\Console\Command;

class ArticleCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'article:publish_notification';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send notification when article is published.';

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
     * @return int
     */
    public function handle()
    {
        $user_token = User::leftJoin('user_role', function ($join) {
            $join->on('users.id', '=', 'user_role.user_id');
        })->where('device_token', '!=', null)->where('device_token', '!=', '')->where('user_role.role_id', 2)->where('receive_notification', 1)->pluck('device_token')->all();
        $user = User::leftJoin('user_role', function ($join) {
            $join->on('users.id', '=', 'user_role.user_id');
        })->where('user_role.role_id', 2)->pluck('users.id')->all();

        $articles = (Article::get());
        foreach ($articles as $article) {
            if (strtotime(date('Y-m-d H:i')) == strtotime(date('Y-m-d H:i', strtotime($article->start_date)))) {
                $title = 'New Article';
                $body = $article->title;
                $notification_data = array("detail" => 'article', "id" => $article->id);
                $user_type = 'customer';

                Helper::configNotification($user_token, $user, $title, $body, $notification_data, $user_type);
            }
        }
    }
}
