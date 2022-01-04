<?php

namespace App\Http\Controllers;

use App\Helper\Helper;
use App\Models\Article;
use App\Models\City;
use App\Models\Collection;
use App\Models\CollectionHub;
use App\Models\CollectionHubRecycleType;
use App\Models\Collector;
use App\Models\Customer;
use App\Models\CustomerMembership;
use App\Models\CustomerPointTransaction;
use App\Models\Level;
use App\Models\Notification;
use App\Models\NotificationRecipient;
use App\Models\RecycleCategory;
use App\Models\Reward;
use App\Models\User;
use App\Models\State;
use Carbon\Carbon;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use Auth;
use DateInterval;
use DatePeriod;
use PHPUnit\TextUI\Help;
use Illuminate\Support\Facades\Mail;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $hub_info = Auth::user()->hub_admin;
        $hubs =  Auth::user()->hub_reader;
        if (in_array(1, Auth::user()->users_roles_id())) {
            $customerCount = Customer::count();
            $collectorCount = Collector::count();
            $collectionCount = Collection::count();
            $hubCount = CollectionHub::count();
            $year = date('Y');

            $dataCollectionsByMonth = array();
            $dataDailyCollectionsByDay = array();
            $dataNewUsersByMonth = array();

            $monthsThisYear = Helper::monthInThisYear();
            $days = Helper::daysInThisMonth();
            $monthsName = $monthsThisYear[1];
            $months = $monthsThisYear[0];

            foreach ($months as $value) {
                $collectionsByMonth =  DB::select("SELECT COUNT(id) as total FROM collection where year(created_at) = '$year' and month(created_at) = '$value'");
                $dataCollectionsByMonth[] = $collectionsByMonth[0]->total;
                $newUsersByMonth =  DB::select("SELECT count(id) as total from customer where year(created_at) = '$year' and month(created_at) = '$value'");
                $dataNewUsersByMonth[] =  $newUsersByMonth[0]->total;
            }
            foreach ($days as $value) {
                $collectionsByDay =  DB::select("SELECT COUNT(id) as total FROM collection where year(created_at) = '2021' and month(created_at) = MONTH(CURDATE()) and day(created_at) = '$value'");
                $dataDailyCollectionsByDay[] = $collectionsByDay[0]->total;
            }

            // $collectionsByMonth =  DB::select("SELECT COUNT(id) as total,substring_index(substring_index(created_at,' ',1),'-',2) as label FROM collection where year(created_at) = '$year' GROUP BY substring_index(substring_index(created_at,' ',1),'-',2)");

            // $collectionsByDay =  DB::select("SELECT count(id) as total,day(created_at) as label from collection where month(created_at) = month(now()) and year(created_at) = '$year' group by day(created_at)");

            // $newUsersByMonth =  DB::select("SELECT count(id) as total, substring_index(substring_index(created_at,' ',1),'-',2) as label from customer where year(created_at) = '$year' GROUP BY substring_index(substring_index(created_at,' ',1),'-',2)");

            $collectionhubs = DB::select("SELECT IFNULL(count(DISTINCT B.id), 0) as total,collection_hub.hub_name FROM ( select (collection.id),collection_hub_id from collection) AS B RIGHT JOIN collection_hub ON B.collection_hub_id = collection_hub.id group by collection_hub.id,collection_hub.hub_name order by total desc");

            $collectionhubs = array_slice($collectionhubs, 0, 5, true);

            return view('home', compact('customerCount', 'collectorCount', 'collectionCount', 'hubCount', 'monthsName', 'dataCollectionsByMonth', 'days', 'dataDailyCollectionsByDay', 'dataNewUsersByMonth', 'collectionhubs'));
        }

        return view('home', compact('hubs'));
    }

    public function getHomeData(Request $request)
    {
        if (in_array(4, Auth::user()->users_roles_id()) || in_array(5, Auth::user()->users_roles_id())) {
            $hub_image = "";
            if ($request->hub_id) {
                $hub_image = CollectionHub::find($request->hub_id)->image;
                if ($hub_image)
                    $hub_image = asset('nucycle-admin/images/hub_logo') . '/' . $hub_image;
                else
                    $hub_image = asset('nucycle-admin/images/hub_logo/user_account.jpg');
            } else {
                $hub_id = Helper::getCollectionHubId();
                $hub = CollectionHub::find($hub_id)->image;
                if (!$hub)
                {
                    $hub = 'user_account.jpg';
                }
                $hub_image = asset('nucycle-admin/images/hub_logo').'/'.$hub;
            }
            if(in_array(5, Auth::user()->users_roles_id()))
            {
                $profile_image = asset('nucycle-admin/images/hub_logo/user_account.jpg');
            }
            else
            {
                $profile_image = $hub_image;
            }
            $hub_ids = '';
            $hub_info = '';
            $week = Helper::dayInWeek();
            $weekLabel = Helper::weekLabel();
            $dayLabel = Helper::daysInThisMonth();
            $monthsThisYear = Helper::monthInThisYear();
            $monthsName = $monthsThisYear[1];
            $months = $monthsThisYear[0];

            $curr_month = date('m');

            if (in_array(5, Auth::user()->users_roles_id())) {
                //     $hub_id = Auth::user()->hub_reader->collection_hub_id;
                if ($request->hub_id == 0) {
                    $hub_ids =  Auth::user()->hub_reader;
                    if (count($hub_ids) == 1) {
                        $hub_info = CollectionHub::find($hub_ids[0]->collection_hub_id);
                    }
                } else {
                    $hub_id = $request->hub_id;
                    $hub_info = CollectionHub::find($hub_id);
                }
            } elseif (in_array(4, Auth::user()->users_roles_id())) {
                $hub_info = Auth::user()->hub_admin->collection_hub;
            }
            // $hub_id = 14;

            // $transactionByWeek = array();
            $transactionByMonth = array();
            // $weightByWeek = array();
            $weightByMonth = array();
            // $newUserByWeek = array();
            $newUserByMonth = array();
            $distinctUserByMonth = array();
            $transactionByDay = array();

            if ($hub_ids) {
                $hub_ids = array_column($hub_ids->toArray(), 'collection_hub_id');

                // foreach ($week as $value) {
                //     $transactionEachWeek = Collection::whereIn('collection_hub_id', $hub_ids)->whereMonth('created_at', $curr_month)->whereDay('created_at', '>=', $value[0])->whereDay('created_at', '<=', $value[1])->get();
                //     array_push($transactionByWeek, $transactionEachWeek->count());

                //     $weightEachWeek = Collection::whereIn('collection_hub_id', $hub_ids)->whereMonth('created_at', $curr_month)->whereDay('created_at', '>=', $value[0])->whereDay('created_at', '<=', $value[1])->selectRaw('IFNULL(sum(total_weight),0) as sum')
                //         ->pluck('sum')->toArray();
                //     array_push($weightByWeek, $weightEachWeek[0]);

                //     $newUserEachWeek = Customer::whereMonth('created_at', $curr_month)->whereDay('created_at', '>=', $value[0])->whereDay('created_at', '<=', $value[1])->get()->count();
                //     array_push($newUserByWeek, $newUserEachWeek);
                // }
                foreach ($months as $value) {
                    $transactionEachMonth = Collection::whereIn('collection_hub_id', $hub_ids)->whereMonth('created_at', $value)->whereYear('created_at', date("Y"));
                    array_push($transactionByMonth, $transactionEachMonth->count());

                    $weightEachMonth = Collection::whereIn('collection_hub_id', $hub_ids)->whereMonth('created_at', $value)->whereYear('created_at', date("Y"))->selectRaw('IFNULL(sum(total_weight),0) as sum')
                        ->pluck('sum')->toArray();
                    array_push($weightByMonth, $weightEachMonth[0]);

                    // $newUserEachMonth = Customer::whereMonth('created_at', $value)->whereYear('created_at', date("Y"))->get()->count();
                    $newUserEachMonth = DB::select("SELECT count(DISTINCT(c.customer_id)) as total, MIN(c.created_at) as min from collection c where c.collection_hub_id in (" . implode(',', array_map('intval', $hub_ids)) . ") group by c.customer_id having month(min) = $value and year(min) = YEAR(CURDATE())");

                    array_push($newUserByMonth, array_column($newUserEachMonth, 'total'));

                    $distinctUser = DB::select("SELECT count(DISTINCT(c.customer_id)) as total from collection c where c.collection_hub_id in (" . implode(',', array_map('intval', $hub_ids)) . ") and month(c.created_at) = $value and year(c.created_at) = YEAR(CURDATE())");

                    array_push($distinctUserByMonth, array_column($distinctUser, 'total'));
                }

                foreach ($dayLabel as $key => $value) {
                    $dayLabel[$key] = $value . '-' . date('F');
                    $transactionEachDay = Collection::whereIn('collection_hub_id', $hub_ids)->whereMonth('created_at', $curr_month)->whereDay('created_at', $value)->get();
                    array_push($transactionByDay, $transactionEachDay->count());
                }

                $weightByCategory = DB::select("SELECT round(IFNULL(sum(B.weight),0),2) as total FROM (SELECT collection_detail.weight, recycle_type.recycle_category_id from collection left join collection_detail on collection.id = collection_detail.collection_id left join recycle_type on collection_detail.recycling_type_id = recycle_type.id where collection_hub_id in (" . implode(',', array_map('intval', $hub_ids)) . ") and month(collection.created_at) = month(current_date()) ) AS B RIGHT JOIN recycle_category on recycle_category.id = B.recycle_category_id group by recycle_category.id,recycle_category.name order by recycle_category.id");
                $weightByCategory = array_column($weightByCategory, 'total');
            } else {
                // foreach ($week as $value) {
                //     $transactionEachWeek = Collection::where('collection_hub_id', $hub_id)->whereMonth('created_at', $curr_month)->whereDay('created_at', '>=', $value[0])->whereDay('created_at', '<=', $value[1])->get();
                //     array_push($transactionByWeek, $transactionEachWeek->count());

                //     $weightEachWeek = Collection::where('collection_hub_id', $hub_id)->whereMonth('created_at', $curr_month)->whereDay('created_at', '>=', $value[0])->whereDay('created_at', '<=', $value[1])->selectRaw('IFNULL(sum(total_weight),0) as sum')
                //         ->pluck('sum')->toArray();
                //     array_push($weightByWeek, $weightEachWeek[0]);

                //     $newUserEachWeek = Customer::whereMonth('created_at', $curr_month)->whereDay('created_at', '>=', $value[0])->whereDay('created_at', '<=', $value[1])->get()->count();
                //     array_push($newUserByWeek, $newUserEachWeek);
                // }
                foreach ($months as $value) {
                    $transactionEachMonth = Collection::where('collection_hub_id', $hub_id)->whereMonth('created_at', $value)->whereYear('created_at', date("Y"))->get();
                    array_push($transactionByMonth, $transactionEachMonth->count());

                    $weightEachMonth = Collection::where('collection_hub_id', $hub_id)->whereMonth('created_at', $value)->whereYear('created_at', date("Y"))->selectRaw('round(IFNULL(sum(total_weight),0),2) as sum')
                        ->pluck('sum')->toArray();
                    array_push($weightByMonth, $weightEachMonth[0]);

                    // $newUserEachMonth = Customer::whereMonth('created_at', $value)->whereYear('created_at', date("Y"))->get()->count();
                    // array_push($newUserByMonth, $newUserEachMonth);

                    $newUserEachMonth = DB::select("SELECT count(DISTINCT(c.customer_id)) as total, MIN(c.created_at) as min from collection c where c.collection_hub_id = $hub_id group by c.customer_id having month(min) = $value and year(min) = YEAR(CURDATE())");

                    array_push($newUserByMonth, array_column($newUserEachMonth, 'total'));

                    $distinctUser = DB::select("SELECT count(DISTINCT(c.customer_id)) as total from collection c where c.collection_hub_id = $hub_id and month(c.created_at) = $value and year(c.created_at) = YEAR(CURDATE())");

                    array_push($distinctUserByMonth, array_column($distinctUser, 'total'));
                }

                foreach ($dayLabel as $key => $value) {
                    $dayLabel[$key] = $value . '-' . date('F');
                    $transactionEachDay = Collection::where('collection_hub_id', $hub_id)->whereMonth('created_at', $curr_month)->whereDay('created_at', $value)->get();
                    array_push($transactionByDay, $transactionEachDay->count());
                }

                $weightByCategory = DB::select("SELECT round(IFNULL(sum(B.weight),0),2) as total FROM (SELECT collection_detail.weight, recycle_type.recycle_category_id from collection left join collection_detail on collection.id = collection_detail.collection_id left join recycle_type on collection_detail.recycling_type_id = recycle_type.id where collection_hub_id = $hub_id and month(collection.created_at) = month(current_date()) ) AS B RIGHT JOIN recycle_category on recycle_category.id = B.recycle_category_id group by recycle_category.id,recycle_category.name order by recycle_category.id");
                $weightByCategory = array_column($weightByCategory, 'total');
            }

            $categoryLabel = RecycleCategory::orderBy('recycle_category.id')->pluck('name')->toArray();
            $colorCategory = Helper::randomColor($categoryLabel);

            $hubs =  Auth::user()->hub_reader;

            return array($weekLabel, $dayLabel, $categoryLabel, $transactionByMonth, $transactionByDay, $weightByMonth, $newUserByMonth, $weightByCategory, $colorCategory, $hubs, $hub_info, $monthsName, $hub_image, $distinctUserByMonth, $profile_image);
        }
    }
}
