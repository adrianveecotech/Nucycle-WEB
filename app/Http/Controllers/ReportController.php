<?php

namespace App\Http\Controllers;

use App\Helper\Helper;
use App\Models\City;
use App\Models\Customer;
use App\Models\CustomerMembership;
use App\Models\State;
use Carbon\Carbon;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use PHPUnit\TextUI\Help;
use Response;
use PDF;
use App\Models\Collection;
use App\Models\CollectionHub;
use App\Models\CollectionHubRecycleType;
use App\Models\Collector;
use App\Models\CustomerReward;
use App\Models\Level;
use App\Models\Merchant;
use App\Models\RecycleCategory;
use App\Models\RewardsCategory;
use DateInterval;
use DatePeriod;
use Spatie\Browsershot\Browsershot;
use Auth;
use App\Exports\NupTableExport;
use App\Exports\EPointTableExport;
use App\Exports\EVoucherTableExport;
use App\Exports\NupSalesTableExport;
use App\Exports\InventoryTableExport;
use App\Exports\ClosingStockTableExport;
use Excel;


class ReportController extends Controller
{
    public function individual_vs_company()
    {
        $individual = Customer::where('isIndividual', 1)->get()->count();
        $company = Customer::where('isIndividual', 0)->get()->count();

        $label = array("Individual", "Company");
        $data = array($individual, $company);

        return view('report.individual_vs_company', compact('label', 'data'));
    }

    public function index()
    {
        return view('report.index');
    }

    public function user_by_state_city()
    {
        $userByState = DB::table('customer')
            ->select(DB::raw('count(customer.id) as "number_of_user", state.name as "state"'))
            ->groupBy('customer.state', 'state.name')
            ->rightJoin('state', 'customer.state', '=', 'state.id')
            ->get()->toArray();

        $label = array_column($userByState, 'state');
        $data = array_column($userByState, 'number_of_user');

        $states = State::get();

        $userByCity = DB::table('customer')
            ->select(DB::raw('count(customer.id) as "number_of_user", city.name as "city",state.name as "state"'))
            ->groupBy('customer.city', 'city.name', 'state.name')
            ->rightJoin('city', 'customer.city', '=', 'city.id')
            ->leftJoin('state', 'city.state_id', '=', 'state.id')
            ->get()->toArray();

        $color = Helper::randomColor($label);

        return view('report.user_by_state_city', compact('label', 'data', 'states', 'color'));
    }

    public function get_city_by_state(Request $request)
    {
        $userByCity = DB::table('customer')
            ->select(DB::raw('count(customer.id) as "number_of_user", city.name as "city"'))
            ->where('city.state_id', $request->state_id)
            ->groupBy('customer.city', 'city.name', 'state.name')
            ->rightJoin('city', 'customer.city', '=', 'city.id')
            ->leftJoin('state', 'city.state_id', '=', 'state.id')
            ->get()->toArray();

        $label = array_column($userByCity, 'city');
        $data = array_column($userByCity, 'number_of_user');
        $color = Helper::randomColor($label);
        $array = array();
        array_push($array, $label, $data, $color);

        return $array;
    }

    public function user_by_membership_tier()
    {
        $userByMembership = DB::table('customer_membership')
            ->select(DB::raw('count(customer_id) as "number_of_user", level.name as "level_name"'))
            ->groupBy('customer_membership.level_id', 'level.name')
            ->rightJoin('level', 'customer_membership.level_id', '=', 'level.id')
            ->get()->toArray();
        $label = array();
        $data = array();
        foreach ($userByMembership as $value) {
            array_push($label, $value->level_name);
            array_push($data, $value->number_of_user);
        }
        return view('report.user_by_membership_tier', compact('label', 'data'));
    }

    public function point_redeemed()
    {
        $userByMembership = DB::table('customer_reward')
            ->select(DB::raw('sum(point_used) as "total_point"'))
            ->whereDate('created_at', '>', Carbon::now()->subDays(30))
            ->get()->toArray();

        return view('report.point_redeemed', compact('userByMembership'));
    }

    public function active_user()
    {
        $states = State::get();
        // $user = DB::table('users')
        //     ->select(DB::raw('count(users.email) as "total",state.name'))
        //     ->where('role_id', 2)
        //     ->whereDate('last_login', '>', Carbon::now()->subDays(30))
        //     ->leftJoin('customer', 'users.id', '=', 'customer.user_id')
        //     ->rightJoin('state', 'customer.state', '=', 'state.id')
        //     ->groupBy('state.id', 'state.name')
        //     ->get()->toArray();
        $user =  DB::select("SELECT count(email) as number_of_user, state.name FROM
                (SELECT users.email as email, customer.state as state_id from users left join user_role on users.id = user_role.user_id left join customer on users.id = customer.user_id where user_role.role_id = '2' and last_login > DATE_SUB((SELECT NOW()), INTERVAL 30 DAY)) AS B
            RIGHT JOIN state ON B.state_id = state.id
            group by state.id,state.name");

        $label = array_column($user, 'name');
        $data = array_column($user, 'number_of_user');
        $color = Helper::randomColor($label);
        return view('report.active_user', compact('user', 'label', 'data', 'states', 'color'));
    }

    public function get_active_user_by_state(Request $request)
    {
        $user =  DB::select("SELECT count(email) as number_of_user, city.name FROM
        (SELECT users.email as email, customer.city as city_id from users left join user_role on users.id = user_role.user_id left join customer on users.id = customer.user_id where user_role.role_id = '2' and last_login > DATE_SUB((SELECT NOW()), INTERVAL 30 DAY)) AS B
    RIGHT JOIN city ON B.city_id = city.id
    WHERE city.state_id = $request->state_id
    group by city.id,city.name");

        $label = array_column($user, 'name');
        $data = array_column($user, 'number_of_user');
        $color = Helper::randomColor($label);
        $array = array();
        array_push($array, $label, $data, $color);

        return $array;
    }

    public function get_point_hub_weekly_monthly(Request $request)
    {
        $dateArr = explode(', ', $request->date);
        $month = $dateArr[0];
        $year = $dateArr[1];
        $month = date_parse($month);
        $month = ($month['month']);
        $result = array();

        $collectionsMonth = DB::select("SELECT IFNULL(sum(all_point), 0) as total_point,collection_hub.hub_name FROM
                (SELECT * FROM collection WHERE month(created_at) = '$month' and year(created_at) = '$year' and collection.status = 1) AS B
            RIGHT JOIN collection_hub ON B.collection_hub_id = collection_hub.id
            group by collection_hub.id,collection_hub.hub_name");

        $week = Helper::dayInWeek();
        foreach ($week as $value) {
            $result1 = array();
            $collectionsWeek = DB::select("SELECT IFNULL(sum(all_point), 0) as total_point,collection_hub.hub_name FROM
            (SELECT * FROM collection WHERE day(created_at) >= '$value[0]' and day(created_at) <= '$value[1]' and month(created_at) = '$month' and year(created_at) = '$year' and collection.status = 1) AS B
        RIGHT JOIN collection_hub ON B.collection_hub_id = collection_hub.id
        group by collection_hub.id,collection_hub.hub_name");
            $hub_name = array_column($collectionsWeek, 'hub_name');
            $total_point = array_column($collectionsWeek, 'total_point');
            array_push($result1, $hub_name, $total_point);
            array_push($result, $result1);
        }

        $label = array_column($collectionsMonth, 'hub_name');
        $data = array_column($collectionsMonth, 'total_point');
        $color = Helper::randomColor($label);
        $return = array($label, $data, $color, $result, $week);

        return $return;
    }

    public function point_hub_weekly_monthly()
    {
        $monthArr = array();
        $userByMembership = DB::table('collection')
            ->where('collection.status',1)
            ->select(DB::raw('created_at'))
            ->get()->toArray();


        foreach ($userByMembership as $value) {
            $month = date("M", strtotime($value->created_at));
            $year = date("Y", strtotime($value->created_at));
            $date = $month . ', ' . $year;
            array_push($monthArr, $date);
        }
        $monthLists = (array_unique($monthArr));
        //         $sevendays = Carbon::now()->subDays(7);
        //         $onemonth = Carbon::now()->subMonth(1);

        //         $sevenDaysPoints = DB::select("SELECT IFNULL(sum(all_point), 0) as total_point,collection_hub.hub_name FROM
        //         (SELECT * FROM collection WHERE collection.created_at > '$sevendays' ) AS B
        //     RIGHT JOIN collection_hub ON B.collection_hub_id = collection_hub.id
        //     group by collection_hub.id,collection_hub.hub_name");

        //         $oneMonthPoints = DB::select("SELECT IFNULL(sum(all_point), 0) as total_point,collection_hub.hub_name FROM
        // (SELECT * FROM collection WHERE collection.created_at > '$onemonth' ) AS B
        // RIGHT JOIN collection_hub ON B.collection_hub_id = collection_hub.id
        // group by collection_hub.id,collection_hub.hub_name");

        return view('report.point_hub_weekly_monthly', compact('monthLists'));
    }

    public function collected_recyling_type()
    {
        $monthArr = array();
        $collectionDetail = DB::table('collection_detail')
            ->select(DB::raw('created_at'))
            ->get()->toArray();


        foreach ($collectionDetail as $value) {
            $month = date("M", strtotime($value->created_at));
            $year = date("Y", strtotime($value->created_at));
            $date = $month . ', ' . $year;
            array_push($monthArr, $date);
        }
        $monthLists = (array_unique($monthArr));

        return view('report.collected_recyling_type', compact('monthLists'));
    }

    public function get_collected_recyling_type(Request $request)
    {
        $dateArr = explode(', ', $request->date);
        $month = $dateArr[0];
        $year = $dateArr[1];
        $month = date_parse($month);
        $month = ($month['month']);
        $result = array();

        $collectionsMonth = DB::select("SELECT IFNULL(sum(weight), 0) as weight,recycle_type.name FROM
                (SELECT * FROM collection_detail WHERE month(created_at) = '$month' and year(created_at) = '$year') AS B
            RIGHT JOIN recycle_type ON B.recycling_type_id = recycle_type.id
            group by recycle_type.id,recycle_type.name");

        $week = Helper::dayInWeek();
        foreach ($week as $value) {
            $result1 = array();
            $collectionsWeek = DB::select("SELECT IFNULL(sum(weight), 0) as weight,recycle_type.name FROM
            (SELECT * FROM collection_detail WHERE day(created_at) >= '$value[0]' and day(created_at) <= '$value[1]' and month(created_at) = '$month' and year(created_at) = '$year') AS B
            RIGHT JOIN recycle_type ON B.recycling_type_id = recycle_type.id
            group by recycle_type.id,recycle_type.name");
            $recycle_type = array_column($collectionsWeek, 'name');
            $weight = array_column($collectionsWeek, 'weight');
            array_push($result1, $recycle_type, $weight);
            array_push($result, $result1);
        }

        $label = array_column($collectionsMonth, 'name');
        $data = array_column($collectionsMonth, 'weight');
        $color = Helper::randomColor($label);
        $return = array($label, $data, $color, $result, $week);

        return $return;
    }

    public function waste_recyling_type()
    {
        $monthArr = array();
        $collectionDetail = DB::table('waste_clearance')
            ->select(DB::raw('created_at'))
            ->get()->toArray();


        foreach ($collectionDetail as $value) {
            $month = date("M", strtotime($value->created_at));
            $year = date("Y", strtotime($value->created_at));
            $date = $month . ', ' . $year;
            array_push($monthArr, $date);
        }
        $monthLists = (array_unique($monthArr));

        return view('report.waste_recyling_type', compact('monthLists'));
    }

    public function get_waste_recyling_type(Request $request)
    {
        $dateArr = explode(', ', $request->date);
        $month = $dateArr[0];
        $year = $dateArr[1];
        $month = date_parse($month);
        $month = ($month['month']);
        $result = array();

        $collectionsMonth = DB::select("SELECT IFNULL(sum(weight), 0) as weight,recycle_type.name FROM
                (SELECT * FROM waste_clearance WHERE month(created_at) = '$month' and year(created_at) = '$year') AS B
            RIGHT JOIN recycle_type ON B.recycle_type_id = recycle_type.id
            group by recycle_type.id,recycle_type.name");

        $week = Helper::dayInWeek();
        foreach ($week as $value) {
            $result1 = array();
            $collectionsWeek = DB::select("SELECT IFNULL(sum(weight), 0) as weight,recycle_type.name FROM
            (SELECT * FROM waste_clearance WHERE day(created_at) >= '$value[0]' and day(created_at) <= '$value[1]' and month(created_at) = '$month' and year(created_at) = '$year') AS B
            RIGHT JOIN recycle_type ON B.recycle_type_id = recycle_type.id
            group by recycle_type.id,recycle_type.name");
            $recycle_type = array_column($collectionsWeek, 'name');
            $weight = array_column($collectionsWeek, 'weight');
            array_push($result1, $recycle_type, $weight);
            array_push($result, $result1);
        }

        $label = array_column($collectionsMonth, 'name');
        $data = array_column($collectionsMonth, 'weight');
        $color = Helper::randomColor($label);
        $return = array($label, $data, $color, $result, $week);

        return $return;
    }

    public function reward_redeemed()
    {
        $monthArr = array();
        $collectionDetail = DB::table('customer_reward')
            ->select(DB::raw('redeem_date'))
            ->get()->toArray();


        foreach ($collectionDetail as $value) {
            $month = date("M", strtotime($value->redeem_date));
            $year = date("Y", strtotime($value->redeem_date));
            $date = $month . ', ' . $year;
            array_push($monthArr, $date);
        }
        $monthLists = (array_unique($monthArr));

        return view('report.reward_redeemed', compact('monthLists'));
    }

    public function get_reward_redeemed(Request $request)
    {
        $dateArr = explode(', ', $request->date);
        $month = $dateArr[0];
        $year = $dateArr[1];
        $month = date_parse($month);
        $month = ($month['month']);
        $result = array();

        $collectionsMonth = DB::select("SELECT IFNULL(count(B.id), 0) as total,reward_category.name FROM
                (SELECT * FROM customer_reward WHERE month(redeem_date) = '$month' and year(redeem_date) = '$year') AS B
            RIGHT JOIN rewards ON B.reward_id  = rewards.id
            RIGHT JOIN reward_category ON rewards.reward_category_id = reward_category.id
            group by reward_category.id,reward_category.name");

        $week = Helper::dayInWeek();
        foreach ($week as $value) {
            $result1 = array();
            $collectionsWeek = DB::select("SELECT IFNULL(count(B.id), 0) as total,reward_category.name FROM
            (SELECT * FROM customer_reward WHERE day(redeem_date) >= '$value[0]' and day(redeem_date) <= '$value[1]' and month(redeem_date) = '$month' and year(redeem_date) = '$year') AS B
            RIGHT JOIN rewards ON B.reward_id  = rewards.id
            RIGHT JOIN reward_category ON rewards.reward_category_id = reward_category.id
            group by reward_category.id,reward_category.name");
            $category = array_column($collectionsWeek, 'name');
            $total = array_column($collectionsWeek, 'total');
            array_push($result1, $category, $total);
            array_push($result, $result1);
        }

        $label = array_column($collectionsMonth, 'name');
        $data = array_column($collectionsMonth, 'total');
        $color = Helper::randomColor($label);
        $return = array($label, $data, $color, $result, $week);

        return $return;
    }

    public function new_vs_exisiting_recycling_frequency()
    {
        $monthArr = array();
        $collectionDetail = DB::table('collection')
            ->where('collection.status',1)
            ->select(DB::raw('created_at'))
            ->get()->toArray();


        foreach ($collectionDetail as $value) {
            $month = date("M", strtotime($value->created_at));
            $year = date("Y", strtotime($value->created_at));
            $date = $month . ', ' . $year;
            array_push($monthArr, $date);
        }
        $monthLists = (array_unique($monthArr));

        return view('report.new_vs_exisiting_recycling_frequency', compact('monthLists'));
    }

    public function get_new_vs_exisiting_recycling_frequency(Request $request)
    {
        $dateArr = explode(', ', $request->date);
        $month = $dateArr[0];
        $year = $dateArr[1];
        $month = date_parse($month);
        $month = ($month['month']);
        $result = array();
        // $newUserMonth = DB::select("SELECT count(collection.id) as total from collection left join customer on collection.customer_id = customer.id where month(collection.created_at) = $month and collection.created_at <= DATE_ADD(customer.created_at, INTERVAL 30 DAY)");
        // $existingUserMonth = DB::select("SELECT count(collection.id) as total from collection left join customer on collection.customer_id = customer.id where month(collection.created_at) = $month and collection.created_at > DATE_ADD(customer.created_at, INTERVAL 30 DAY)");
        $newUserMonth = DB::select("SELECT count(collection.id) as total from collection left join customer on collection.customer_id = customer.id where year(collection.created_at) = $year and month(collection.created_at) = $month and year(customer.created_at) = $year and month(customer.created_at) = $month and collection.status = 1 ");
        $existingUserMonth = DB::select("SELECT count(collection.id) as total from collection left join customer on collection.customer_id = customer.id where year(collection.created_at) = $year and month(collection.created_at) = $month and not (month(customer.created_at) = $month and year(customer.created_at) = $year) and collection.status = 1 ");
        $existingUserMonth = array_column($existingUserMonth, 'total');
        $newUserMonth = array_column($newUserMonth, 'total');
        $data = array();
        $label = array('Existing User', 'New User');

        array_push($data, $existingUserMonth[0], $newUserMonth[0]);
        $color = Helper::randomColor(2);

        $week = Helper::dayInWeek();
        foreach ($week as $value) {
            $result1 = array();
            // $newUserWeek = DB::select("SELECT count(collection.id) as total from collection left join customer on collection.customer_id = customer.id where month(collection.created_at) = $month and day(collection.created_at) >= '$value[0]' and day(collection.created_at) <= '$value[1]' and collection.created_at <= DATE_ADD(customer.created_at, INTERVAL 30 DAY)");
            // $existingUserWeek = DB::select("SELECT count(collection.id) as total from collection left join customer on collection.customer_id = customer.id where month(collection.created_at) = $month and day(collection.created_at) >= '$value[0]' and day(collection.created_at) <= '$value[1]' and collection.created_at > DATE_ADD(customer.created_at, INTERVAL 30 DAY)");

            $newUserWeek = DB::select("SELECT count(collection.id) as total from collection left join customer on collection.customer_id = customer.id where month(collection.created_at) = $month and day(collection.created_at) >= '$value[0]' and day(collection.created_at) <= '$value[1]' and month(customer.created_at) = $month and day(customer.created_at)  >= '$value[0]' and day(customer.created_at) <= '$value[1]' and collection.status = 1 ");
            $existingUserWeek = DB::select("SELECT count(collection.id) as total from collection left join customer on collection.customer_id = customer.id where month(collection.created_at) = $month and day(collection.created_at) >= '$value[0]' and day(collection.created_at) <= '$value[1]' and collection.status = 1 and not(month(customer.created_at) = $month and day(customer.created_at) >= '$value[0]' and day(customer.created_at) <= '$value[1]')");

            array_push($result1, $existingUserWeek[0]->total, $newUserWeek[0]->total);
            array_push($result, $result1);
        }
        $return = array($data, $label, $color, $result, $week);

        return $return;
    }

    public function individual_vs_company_visited_center()
    {

        $individual =  DB::select("SELECT IFNULL(count(DISTINCT B.customer_id), 0) as total_point,collection_hub.hub_name FROM (SELECT collection.* FROM collection left join customer on collection.customer_id = customer.id where customer.isIndividual = 1 and collection.status = 1 ) AS B RIGHT JOIN collection_hub ON B.collection_hub_id = collection_hub.id group by collection_hub.id,collection_hub.hub_name");

        $company =  DB::select("SELECT IFNULL(count(DISTINCT B.customer_id), 0) as total_point,collection_hub.hub_name FROM (SELECT collection.* FROM collection left join customer on collection.customer_id = customer.id where customer.isIndividual = 0 and collection.status = 1 ) AS B RIGHT JOIN collection_hub ON B.collection_hub_id = collection_hub.id group by collection_hub.id,collection_hub.hub_name");
        $label = array_column($individual, 'hub_name');
        $individual = array_column($individual, 'total_point');
        $company = array_column($company, 'total_point');

        $data = array($individual, $company);

        return view('report.individual_vs_company_visited_center', compact('label', 'data'));
    }

    public function new_vs_existing_visited_center()
    {

        $newUser =  DB::select("SELECT IFNULL(count(DISTINCT B.customer_id), 0) as total,collection_hub.hub_name  FROM ( select distinct (collection.customer_id),collection_hub_id from collection left join customer on collection.customer_id = customer.id where collection.status = 1 and customer.created_at >= DATE_SUB((SELECT NOW()), INTERVAL 30 DAY)) AS B RIGHT JOIN collection_hub ON B.collection_hub_id = collection_hub.id group by collection_hub.id,collection_hub.hub_name order by collection_hub.id asc");

        $existingUser =  DB::select("SELECT IFNULL(count(DISTINCT B.customer_id), 0) as total,collection_hub.hub_name FROM ( select distinct (collection.customer_id),collection_hub_id from collection left join customer on collection.customer_id = customer.id where collection.status = 1 and customer.created_at < DATE_SUB((SELECT NOW()), INTERVAL 30 DAY)) AS B RIGHT JOIN collection_hub ON B.collection_hub_id = collection_hub.id group by collection_hub.id,collection_hub.hub_name order by collection_hub.id asc");

        $label = array_column($newUser, 'hub_name');
        $newUser = array_column($newUser, 'total');
        $existingUser = array_column($existingUser, 'total');

        return view('report.new_vs_existing_visited_center', compact('label', 'newUser', 'existingUser'));
    }

    public function collection_total()
    {
        $allStates = State::get()->toArray();
        return view('report.collection.total', compact('allStates'));
    }

    public function total_collected_waste()
    {
        $allStates = State::get()->toArray();
        return view('report.collection.total_collected_waste', compact('allStates'));
    }

    public function total_waste_selling()
    {
        $allStates = State::get()->toArray();
        return view('report.collection.total_waste_selling', compact('allStates'));
    }

    public function collection_on_site()
    {
        $allStates = State::get()->toArray();
        return view('report.collection.on_site', compact('allStates'));
    }

    public function on_site_collected_waste()
    {
        $allStates = State::get()->toArray();
        return view('report.collection.on_site_collected_waste', compact('allStates'));
    }

    public function on_site_waste_selling()
    {
        $allStates = State::get()->toArray();
        return view('report.collection.on_site_waste_selling', compact('allStates'));
    }

    public function collection_mobile()
    {
        $allStates = State::get()->toArray();
        return view('report.collection.mobile', compact('allStates'));
    }

    public function mobile_collected_waste()
    {
        $allStates = State::get()->toArray();
        return view('report.collection.mobile_collected_waste', compact('allStates'));
    }

    public function mobile_waste_selling()
    {
        $allStates = State::get()->toArray();
        return view('report.collection.mobile_waste_selling', compact('allStates'));
    }

    public function get_collection_collected_transaction_data(Request $request)
    {
        $hub_type = $request->hub_type;
        $curr_mon = date('m');
        $type = $request->type;
        $format = $request->format;
        $year = date("Y");
        $months = Helper::monthInThisYear();
        $weeks = Helper::dayInWeek();
        $weeksLabel = Helper::weekLabel();
        $allStates = State::get()->toArray();
        $colors = Helper::randomColor($allStates);
        $allStatesName = array();
        $date_from = $request->date_from;
        $date_to = $request->date_to;
        $labels = array();
        $tableData = array();
        if ($date_from != '' && $date_to != '') {
            $months_name = array();
            $start    = (new DateTime($date_from))->modify('first day of this month');
            $end      = (new DateTime($date_to))->modify('first day of next month');
            $interval = DateInterval::createFromDateString('1 month');
            $period   = new DatePeriod($start, $interval, $end);
            $dayOfStartDate = date('d', strtotime($date_from));
            $dayOfEndDate = date('d', strtotime($date_to));
            $monthOfEndDate = date('m', strtotime($date_to));

            $stateData = array();
            if ($hub_type == '') {
                $tableData = DB::select("SELECT cus.email, count(cus.email) as total, IF(cus.isIndividual=1, 'B2C', 'B2B') as type from collection c left join customer cus on cus.id = c.customer_id left join collection_hub ch on ch.id = c.collection_hub_id where c.created_at >= '$date_from' and c.created_at <= '$date_to' and c.status = 1 group by cus.email,cus.isIndividual");
            } else {
                $tableData = DB::select("SELECT cus.email, count(cus.email) as total,IF(cus.isIndividual=1, 'B2C', 'B2B') as type from collection c left join customer cus on cus.id = c.customer_id left join collection_hub ch on ch.id = c.collection_hub_id where ch.type = '$hub_type' and c.created_at >= '$date_from' and c.created_at <= '$date_to' and c.status = 1 group by cus.email,cus.isIndividual");
            }
        } else {
            if ($hub_type == '') {
                $tableData = DB::select("SELECT cus.email, count(cus.email) as total,IF(cus.isIndividual=1, 'B2C', 'B2B') as type from collection c left join customer cus on cus.id = c.customer_id left join collection_hub ch on ch.id = c.collection_hub_id where c.status = 1 group by cus.email,cus.isIndividual");
            } else {
                $tableData = DB::select("SELECT cus.email, count(cus.email) as total,IF(cus.isIndividual=1, 'B2C', 'B2B') as type from collection c left join customer cus on cus.id = c.customer_id left join collection_hub ch on ch.id = c.collection_hub_id where ch.type = '$hub_type' and c.status = 1 group by cus.email,cus.isIndividual");
            }
        }
        if ($format == 'Month') {
            $labels = $months[1];
            foreach ($allStates as $index => $value) {
                if ($type == 'User') {
                    if ($date_from == '' && $date_to == '') {
                        $stateData = array();
                        foreach ($months[0] as $value1) {
                            if ($index == 0) {
                                if ($hub_type == '') {
                                    $companyEachMonth = DB::select("SELECT count(collection.id) as total FROM collection left join collection_hub on collection.collection_hub_id = collection_hub.id left join customer on collection.customer_id = customer.id where customer.isIndividual = 0 and year(collection.created_at) = '$year' and collection.status = 1 and month(collection.created_at) = '$value1'");

                                    $individualEachMonth = DB::select("SELECT count(collection.id)  as total FROM collection left join collection_hub on collection.collection_hub_id = collection_hub.id left join customer on collection.customer_id = customer.id where customer.isIndividual = 1 and year(collection.created_at) = '$year' and collection.status = 1 and month(collection.created_at) = '$value1'");

                                    $userEachMonth = DB::select("SELECT count(collection.id) as total FROM collection left join collection_hub on collection.collection_hub_id = collection_hub.id left join customer on collection.customer_id = customer.id where  year(collection.created_at) = '$year' and collection.status = 1 and month(collection.created_at) = '$value1'");
                                } else {
                                    $companyEachMonth = DB::select("SELECT count(collection.id) as total FROM collection left join collection_hub on collection.collection_hub_id = collection_hub.id left join customer on collection.customer_id = customer.id where collection_hub.type = $hub_type and customer.isIndividual = 0 and collection.status = 1 and year(collection.created_at) = '$year' and month(collection.created_at) = '$value1'");

                                    $individualEachMonth = DB::select("SELECT count(collection.id)  as total FROM collection left join collection_hub on collection.collection_hub_id = collection_hub.id left join customer on collection.customer_id = customer.id where collection_hub.type = $hub_type and customer.isIndividual = 1 and collection.status = 1 and year(collection.created_at) = '$year' and month(collection.created_at) = '$value1'");

                                    $userEachMonth = DB::select("SELECT count(collection.id) as total FROM collection left join collection_hub on collection.collection_hub_id = collection_hub.id left join customer on collection.customer_id = customer.id where collection_hub.type = $hub_type and collection.status = 1 and year(collection.created_at) = '$year' and month(collection.created_at) = '$value1'");
                                }


                                $companyMonth[] = array_column($companyEachMonth, 'total')[0];
                                $individualMonth[] = array_column($individualEachMonth, 'total')[0];
                                $userMonth[] = array_column($userEachMonth, 'total')[0];
                            }
                            if ($hub_type == '') {
                                $transactionMonthState = DB::select("SELECT count(collection.id) as total FROM collection left join collection_hub on collection.collection_hub_id = collection_hub.id left join customer on collection.customer_id = customer.id left join state on customer.state = state.id where customer.state = ? and year(collection.created_at) = '$year' and collection.status = 1 and month(collection.created_at) = '$value1'", array($value['id']));
                            } else {
                                $transactionMonthState = DB::select("SELECT count(collection.id) as total FROM collection left join collection_hub on collection.collection_hub_id = collection_hub.id left join customer on collection.customer_id = customer.id left join state on customer.state = state.id where collection_hub.type = $hub_type and customer.state = ? and year(collection.created_at) = '$year' and collection.status = 1 and month(collection.created_at) = '$value1'", array($value['id']));
                            }

                            $stateData[] = array_column($transactionMonthState, 'total')[0];
                        }
                        $dataAllStates[] = $stateData;
                    } else {
                        $stateData = array();
                        foreach ($period as $key => $dt) {
                            $year = $dt->format('Y');
                            $month = $dt->format('m');

                            if ($index == 0) {
                                if ($hub_type == '') {
                                    array_push($months_name, $dt->format('M y'));
                                    $companyEachMonth = DB::select("SELECT count(collection.id) as total FROM collection left join collection_hub on collection.collection_hub_id = collection_hub.id left join customer on collection.customer_id = customer.id where customer.isIndividual = 0 and year(collection.created_at) = '$year' and month(collection.created_at) = '$month' and collection.status = 1 and collection.created_at >= '$date_from' and collection.created_at <= '$date_to'");

                                    $individualEachMonth = DB::select("SELECT count(collection.id)  as total FROM collection left join collection_hub on collection.collection_hub_id = collection_hub.id left join customer on collection.customer_id = customer.id where customer.isIndividual = 1 and year(collection.created_at) = '$year' and month(collection.created_at) = '$month' and collection.status = 1 and collection.created_at >= '$date_from' and collection.created_at <= '$date_to'");

                                    $userEachMonth = DB::select("SELECT count(collection.id) as total FROM collection left join collection_hub on collection.collection_hub_id = collection_hub.id left join customer on collection.customer_id = customer.id where  year(collection.created_at) = '$year' and month(collection.created_at) = '$month' and collection.created_at >= '$date_from' and collection.status = 1 and collection.created_at <= '$date_to'");
                                } else {
                                    array_push($months_name, $dt->format('M y'));
                                    $companyEachMonth = DB::select("SELECT count(collection.id) as total FROM collection left join collection_hub on collection.collection_hub_id = collection_hub.id left join customer on collection.customer_id = customer.id where collection_hub.type = $hub_type and customer.isIndividual = 0 and year(collection.created_at) = '$year' and collection.status = 1 and month(collection.created_at) = '$month' and collection.created_at >= '$date_from' and collection.created_at <= '$date_to'");

                                    $individualEachMonth = DB::select("SELECT count(collection.id)  as total FROM collection left join collection_hub on collection.collection_hub_id = collection_hub.id left join customer on collection.customer_id = customer.id where collection_hub.type = $hub_type and customer.isIndividual = 1 and year(collection.created_at) = '$year' and collection.status = 1 and month(collection.created_at) = '$month' and collection.created_at >= '$date_from' and collection.created_at <= '$date_to'");

                                    $userEachMonth = DB::select("SELECT count(collection.id) as total FROM collection left join collection_hub on collection.collection_hub_id = collection_hub.id left join customer on collection.customer_id = customer.id where collection_hub.type = $hub_type and year(collection.created_at) = '$year' and month(collection.created_at) = '$month' and collection.status = 1 and collection.created_at >= '$date_from' and collection.created_at <= '$date_to'");
                                }


                                $companyMonth[] = array_column($companyEachMonth, 'total')[0];
                                $individualMonth[] = array_column($individualEachMonth, 'total')[0];
                                $userMonth[] = array_column($userEachMonth, 'total')[0];
                            }
                            $state_id = $value['id'];
                            if ($hub_type == '') {
                                $transactionMonthState = DB::select("SELECT count(collection.id) as total FROM collection left join collection_hub on collection.collection_hub_id = collection_hub.id left join customer on collection.customer_id = customer.id left join state on customer.state = state.id where customer.state = $state_id and year(collection.created_at) = '$year' and collection.status = 1 and month(collection.created_at) = '$month' and collection.created_at >= '$date_from' and collection.created_at <= '$date_to'");
                                $stateData[] = array_column($transactionMonthState, 'total')[0];
                            } else {
                                $transactionMonthState = DB::select("SELECT count(collection.id) as total FROM collection left join collection_hub on collection.collection_hub_id = collection_hub.id left join customer on collection.customer_id = customer.id left join state on customer.state = state.id where collection_hub.type = $hub_type and customer.state = $state_id and year(collection.created_at) = '$year' and month(collection.created_at) = '$month' and collection.created_at >= '$date_from' and collection.status = 1 and collection.created_at <= '$date_to'");
                                $stateData[] = array_column($transactionMonthState, 'total')[0];
                            }
                        }
                        $labels = $months_name;

                        $dataAllStates[] = $stateData;
                    }
                } else if ($type == 'Hub') {
                    if ($date_from == '' && $date_to == '') {
                        $stateCompanyData = array();
                        $stateIndividualData = array();
                        foreach ($months[0] as $value1) {
                            if ($index == 0) {
                                if ($hub_type == '') {
                                    $companyEachMonth = DB::select("SELECT count(collection.id) as total FROM collection left join collection_hub on collection.collection_hub_id = collection_hub.id left join customer on collection.customer_id = customer.id where  customer.isIndividual = 0 and year(collection.created_at) = '$year' and collection.status = 1 and month(collection.created_at) = '$value1'");

                                    $individualEachMonth = DB::select("SELECT count(collection.id)  as total FROM collection left join collection_hub on collection.collection_hub_id = collection_hub.id left join customer on collection.customer_id = customer.id where  customer.isIndividual = 1 and year(collection.created_at) = '$year' and collection.status = 1 and month(collection.created_at) = '$value1'");
                                } else {
                                    $companyEachMonth = DB::select("SELECT count(collection.id) as total FROM collection left join collection_hub on collection.collection_hub_id = collection_hub.id left join customer on collection.customer_id = customer.id where collection_hub.type = $hub_type and customer.isIndividual = 0 and collection.status = 1 and year(collection.created_at) = '$year' and month(collection.created_at) = '$value1'");

                                    $individualEachMonth = DB::select("SELECT count(collection.id)  as total FROM collection left join collection_hub on collection.collection_hub_id = collection_hub.id left join customer on collection.customer_id = customer.id where collection_hub.type = $hub_type and customer.isIndividual = 1 and collection.status = 1 and year(collection.created_at) = '$year' and month(collection.created_at) = '$value1'");
                                }
                                $companyMonth[] = array_column($companyEachMonth, 'total')[0];
                                $individualMonth[] = array_column($individualEachMonth, 'total')[0];
                            }
                            if ($hub_type == '') {
                                $companyTransactionMonthState = DB::select("SELECT count(collection.id) as total FROM collection left join collection_hub on collection.collection_hub_id = collection_hub.id left join customer on collection.customer_id = customer.id left join state on customer.state = state.id where customer.isIndividual = 0 and collection.status = 1 and collection_hub.hub_state_id = ? and year(collection.created_at) = '$year' and month(collection.created_at) = '$value1'", array($value['id']));

                                $individualTransactionMonthState = DB::select("SELECT count(collection.id) as total FROM collection left join collection_hub on collection.collection_hub_id = collection_hub.id left join customer on collection.customer_id = customer.id left join state on customer.state = state.id where customer.isIndividual = 1 and collection.status = 1 and collection_hub.hub_state_id = ? and year(collection.created_at) = '$year' and month(collection.created_at) = '$value1'", array($value['id']));
                            } else {
                                $companyTransactionMonthState = DB::select("SELECT count(collection.id) as total FROM collection left join collection_hub on collection.collection_hub_id = collection_hub.id left join customer on collection.customer_id = customer.id left join state on customer.state = state.id where collection_hub.type = $hub_type  and customer.isIndividual = 0 and collection_hub.hub_state_id = ? and year(collection.created_at) = '$year' and collection.status = 1 and month(collection.created_at) = '$value1'", array($value['id']));

                                $individualTransactionMonthState = DB::select("SELECT count(collection.id) as total FROM collection left join collection_hub on collection.collection_hub_id = collection_hub.id left join customer on collection.customer_id = customer.id left join state on customer.state = state.id where collection_hub.type = $hub_type  and customer.isIndividual = 1 and collection_hub.hub_state_id = ? and year(collection.created_at) = '$year' and collection.status = 1 and month(collection.created_at) = '$value1'", array($value['id']));
                            }
                            $stateCompanyData[] = array_column($companyTransactionMonthState, 'total')[0];
                            $stateIndividualData[] = array_column($individualTransactionMonthState, 'total')[0];
                        }
                        $dataAllStatesCompany[] = $stateCompanyData;
                        $dataAllStatesIndividual[] = $stateIndividualData;
                        if ($index == 0) {
                            $color =  Helper::randomColor($allStates);
                            $colors = array_merge($colors, $color);
                        }
                    } else {
                        $stateCompanyData = array();
                        $stateIndividualData = array();
                        foreach ($period as $key => $dt) {
                            $year = $dt->format('Y');
                            $month = $dt->format('m');

                            if ($index == 0) {
                                array_push($months_name, $dt->format('M y'));
                                if ($hub_type == '') {
                                    $companyEachMonth = DB::select("SELECT count(collection.id) as total FROM collection left join collection_hub on collection.collection_hub_id = collection_hub.id left join customer on collection.customer_id = customer.id where customer.isIndividual = 0 and year(collection.created_at) = '$year' and collection.status = 1 and month(collection.created_at) = '$month' and collection.created_at >= '$date_from' and collection.created_at <= '$date_to'");

                                    $individualEachMonth = DB::select("SELECT count(collection.id)  as total FROM collection left join collection_hub on collection.collection_hub_id = collection_hub.id left join customer on collection.customer_id = customer.id where customer.isIndividual = 1 and year(collection.created_at) = '$year' and collection.status = 1 and month(collection.created_at) = '$month' and collection.created_at >= '$date_from' and collection.created_at <= '$date_to'");
                                } else {
                                    $companyEachMonth = DB::select("SELECT count(collection.id) as total FROM collection left join collection_hub on collection.collection_hub_id = collection_hub.id left join customer on collection.customer_id = customer.id where collection_hub.type = $hub_type and customer.isIndividual = 0 and collection.status = 1 and year(collection.created_at) = '$year' and month(collection.created_at) = '$month' and collection.created_at >= '$date_from' and collection.created_at <= '$date_to'");

                                    $individualEachMonth = DB::select("SELECT count(collection.id)  as total FROM collection left join collection_hub on collection.collection_hub_id = collection_hub.id left join customer on collection.customer_id = customer.id where collection_hub.type = $hub_type and customer.isIndividual = 1 and collection.status = 1 and year(collection.created_at) = '$year' and month(collection.created_at) = '$month' and collection.created_at >= '$date_from' and collection.created_at <= '$date_to'");
                                }


                                $companyMonth[] = array_column($companyEachMonth, 'total')[0];
                                $individualMonth[] = array_column($individualEachMonth, 'total')[0];
                            }
                            if ($hub_type == '') {
                                $companyTransactionMonthState = DB::select("SELECT count(collection.id) as total FROM collection left join collection_hub on collection.collection_hub_id = collection_hub.id left join customer on collection.customer_id = customer.id left join state on customer.state = state.id where customer.isIndividual = 0 and collection_hub.hub_state_id = ? and year(collection.created_at) = '$year' and month(collection.created_at) = '$month' and collection.created_at >= '$date_from' and collection.status = 1 and collection.created_at <= '$date_to'", array($value['id']));

                                $individualTransactionMonthState = DB::select("SELECT count(collection.id) as total FROM collection left join collection_hub on collection.collection_hub_id = collection_hub.id left join customer on collection.customer_id = customer.id left join state on customer.state = state.id where customer.isIndividual = 1 and collection_hub.hub_state_id = ? and year(collection.created_at) = '$year' and month(collection.created_at) = '$month' and collection.created_at >= '$date_from' and collection.status = 1 and collection.created_at <= '$date_to'", array($value['id']));
                            } else {
                                $companyTransactionMonthState = DB::select("SELECT count(collection.id) as total FROM collection left join collection_hub on collection.collection_hub_id = collection_hub.id left join customer on collection.customer_id = customer.id left join state on customer.state = state.id where collection_hub.type = $hub_type  and customer.isIndividual = 0 and collection_hub.hub_state_id = ? and year(collection.created_at) = '$year' and month(collection.created_at) = '$month' and collection.status = 1 and collection.created_at >= '$date_from' and collection.created_at <= '$date_to'", array($value['id']));

                                $individualTransactionMonthState = DB::select("SELECT count(collection.id) as total FROM collection left join collection_hub on collection.collection_hub_id = collection_hub.id left join customer on collection.customer_id = customer.id left join state on customer.state = state.id where collection_hub.type = $hub_type  and customer.isIndividual = 1 and collection_hub.hub_state_id = ? and year(collection.created_at) = '$year' and collection.status = 1 and month(collection.created_at) = '$month' and collection.created_at >= '$date_from' and collection.created_at <= '$date_to'", array($value['id']));
                            }
                            $stateCompanyData[] = array_column($companyTransactionMonthState, 'total')[0];
                            $stateIndividualData[] = array_column($individualTransactionMonthState, 'total')[0];
                        }
                        $labels = $months_name;
                        $dataAllStatesCompany[] = $stateCompanyData;
                        $dataAllStatesIndividual[] = $stateIndividualData;
                        if ($index == 0) {
                            $color =  Helper::randomColor($allStates);
                            $colors = array_merge($colors, $color);
                        }
                    }
                }

                $allStatesName[] = $value['name'];
            }
        }

        if ($format == 'Week') {
            foreach ($allStates as $index => $value) {
                if ($type == 'User') {
                    if ($date_from == '' && $date_to == '') {
                        $stateData = array();
                        foreach ($weeks as $value1) {
                            if ($index == 0) {
                                if ($hub_type == '') {
                                    $companyEachMonth = DB::select("SELECT count(collection.id) as total FROM collection left join collection_hub on collection.collection_hub_id = collection_hub.id left join customer on collection.customer_id = customer.id where customer.isIndividual = 0 and year(collection.created_at) = '$year' and collection.status = 1 and month(collection.created_at) = '$curr_mon' and day(collection.created_at) >= '$value1[0]' and day(collection.created_at) <= '$value1[1]'");

                                    $individualEachMonth = DB::select("SELECT count(collection.id)  as total FROM collection left join collection_hub on collection.collection_hub_id = collection_hub.id left join customer on collection.customer_id = customer.id where customer.isIndividual = 1 and year(collection.created_at) = '$year' and collection.status = 1 and month(collection.created_at) = '$curr_mon' and day(collection.created_at) >= '$value1[0]' and day(collection.created_at) <= '$value1[1]'");

                                    $userEachMonth = DB::select("SELECT count(collection.id) as total FROM collection left join collection_hub on collection.collection_hub_id = collection_hub.id left join customer on collection.customer_id = customer.id where year(collection.created_at) = '$year' and month(collection.created_at) = '$curr_mon' and collection.status = 1 and day(collection.created_at) >= '$value1[0]' and day(collection.created_at) <= '$value1[1]'");
                                } else {
                                    $companyEachMonth = DB::select("SELECT count(collection.id) as total FROM collection left join collection_hub on collection.collection_hub_id = collection_hub.id left join customer on collection.customer_id = customer.id where collection_hub.type = $hub_type and customer.isIndividual = 0 and year(collection.created_at) = '$year' and collection.status = 1 and month(collection.created_at) = '$curr_mon' and day(collection.created_at) >= '$value1[0]' and day(collection.created_at) <= '$value1[1]'");

                                    $individualEachMonth = DB::select("SELECT count(collection.id)  as total FROM collection left join collection_hub on collection.collection_hub_id = collection_hub.id left join customer on collection.customer_id = customer.id where collection_hub.type = $hub_type and customer.isIndividual = 1 and year(collection.created_at) = '$year' and collection.status = 1 and month(collection.created_at) = '$curr_mon' and day(collection.created_at) >= '$value1[0]' and day(collection.created_at) <= '$value1[1]'");

                                    $userEachMonth = DB::select("SELECT count(collection.id) as total FROM collection left join collection_hub on collection.collection_hub_id = collection_hub.id left join customer on collection.customer_id = customer.id where collection_hub.type = $hub_type and year(collection.created_at) = '$year' and collection.status = 1 and month(collection.created_at) = '$curr_mon' and day(collection.created_at) >= '$value1[0]' and day(collection.created_at) <= '$value1[1]'");
                                }
                                $companyMonth[] = array_column($companyEachMonth, 'total')[0];
                                $individualMonth[] = array_column($individualEachMonth, 'total')[0];
                                $userMonth[] = array_column($userEachMonth, 'total')[0];
                            }
                            if ($hub_type == '') {
                                $transactionMonthState = DB::select("SELECT count(collection.id) as total FROM collection left join collection_hub on collection.collection_hub_id = collection_hub.id left join customer on collection.customer_id = customer.id left join state on customer.state = state.id where customer.state = ? and year(collection.created_at) = '$year' and collection.status = 1 and month(collection.created_at) = '$curr_mon' and day(collection.created_at) >= '$value1[0]' and day(collection.created_at) <= '$value1[1]'", array($value['id']));
                            } else {
                                $transactionMonthState = DB::select("SELECT count(collection.id) as total FROM collection left join collection_hub on collection.collection_hub_id = collection_hub.id left join customer on collection.customer_id = customer.id left join state on customer.state = state.id where collection_hub.type = $hub_type and customer.state = ? and year(collection.created_at) = '$year' and collection.status = 1 and month(collection.created_at) = '$curr_mon' and day(collection.created_at) >= '$value1[0]' and day(collection.created_at) <= '$value1[1]'", array($value['id']));
                            }
                            $stateData[] = array_column($transactionMonthState, 'total')[0];
                        }
                        $dataAllStates[] = $stateData;
                        $labels = $weeksLabel;
                    } else {
                        $weeks_name = array();
                        $stateData = array();
                        foreach ($period as $key => $dt) {
                            $break = false;
                            $curr_year = $dt->format('Y');
                            $curr_mon = $dt->format('m');
                            foreach ($weeks as $key1 => $value1) {
                                if ($break == true)
                                    break;
                                if ($key == 0) {
                                    if ($dayOfStartDate >= $value1[0] && $dayOfStartDate <= $value1[1] == false) {
                                        continue;
                                    }
                                }
                                if ($monthOfEndDate == $curr_mon) {
                                    if ($dayOfEndDate >= $value1[0] && $dayOfEndDate <= $value1[1]) {
                                        $break = true;
                                    }
                                }

                                if ($index == 0) {
                                    array_push($weeks_name, $dt->format('Y M ') . $weeksLabel[$key1]);
                                    if ($hub_type == '') {
                                        $companyEachMonth = DB::select("SELECT count(collection.id) as total FROM collection left join collection_hub on collection.collection_hub_id = collection_hub.id left join customer on collection.customer_id = customer.id where customer.isIndividual = 0 and year(collection.created_at) = '$curr_year' and collection.status = 1 and month(collection.created_at) = '$curr_mon' and day(collection.created_at) >= '$value1[0]' and day(collection.created_at) <= '$value1[1]'");

                                        $individualEachMonth = DB::select("SELECT count(collection.id)  as total FROM collection left join collection_hub on collection.collection_hub_id = collection_hub.id left join customer on collection.customer_id = customer.id where customer.isIndividual = 1 and year(collection.created_at) = '$curr_year' and collection.status = 1 and month(collection.created_at) = '$curr_mon' and day(collection.created_at) >= '$value1[0]' and day(collection.created_at) <= '$value1[1]'");

                                        $userEachMonth = DB::select("SELECT count(collection.id) as total FROM collection left join collection_hub on collection.collection_hub_id = collection_hub.id left join customer on collection.customer_id = customer.id where year(collection.created_at) = '$curr_year' and collection.status = 1 and month(collection.created_at) = '$curr_mon' and day(collection.created_at) >= '$value1[0]' and day(collection.created_at) <= '$value1[1]'");
                                    } else {
                                        $companyEachMonth = DB::select("SELECT count(collection.id) as total FROM collection left join collection_hub on collection.collection_hub_id = collection_hub.id left join customer on collection.customer_id = customer.id where collection_hub.type = $hub_type and customer.isIndividual = 0 and year(collection.created_at) = '$curr_year' and collection.status = 1 and month(collection.created_at) = '$curr_mon' and day(collection.created_at) >= '$value1[0]' and day(collection.created_at) <= '$value1[1]'");

                                        $individualEachMonth = DB::select("SELECT count(collection.id)  as total FROM collection left join collection_hub on collection.collection_hub_id = collection_hub.id left join customer on collection.customer_id = customer.id where collection_hub.type = $hub_type and customer.isIndividual = 1 and year(collection.created_at) = '$curr_year' and collection.status = 1 and month(collection.created_at) = '$curr_mon' and day(collection.created_at) >= '$value1[0]' and day(collection.created_at) <= '$value1[1]'");

                                        $userEachMonth = DB::select("SELECT count(collection.id) as total FROM collection left join collection_hub on collection.collection_hub_id = collection_hub.id left join customer on collection.customer_id = customer.id where collection_hub.type = $hub_type and year(collection.created_at) = '$curr_year' and collection.status = 1 and month(collection.created_at) = '$curr_mon' and day(collection.created_at) >= '$value1[0]' and day(collection.created_at) <= '$value1[1]'");
                                    }
                                    $companyMonth[] = array_column($companyEachMonth, 'total')[0];
                                    $individualMonth[] = array_column($individualEachMonth, 'total')[0];
                                    $userMonth[] = array_column($userEachMonth, 'total')[0];
                                }
                                if ($hub_type == '') {
                                    $transactionMonthState = DB::select("SELECT count(collection.id) as total FROM collection left join collection_hub on collection.collection_hub_id = collection_hub.id left join customer on collection.customer_id = customer.id left join state on customer.state = state.id where customer.state = ? and year(collection.created_at) = '$curr_year' and collection.status = 1 and month(collection.created_at) = '$curr_mon' and day(collection.created_at) >= '$value1[0]' and day(collection.created_at) <= '$value1[1]'", array($value['id']));
                                } else {
                                    $transactionMonthState = DB::select("SELECT count(collection.id) as total FROM collection left join collection_hub on collection.collection_hub_id = collection_hub.id left join customer on collection.customer_id = customer.id left join state on customer.state = state.id where collection_hub.type = $hub_type and customer.state = ? and year(collection.created_at) = '$curr_year' and collection.status = 1 and month(collection.created_at) = '$curr_mon' and day(collection.created_at) >= '$value1[0]' and day(collection.created_at) <= '$value1[1]'", array($value['id']));
                                }

                                $stateData[] = array_column($transactionMonthState, 'total')[0];
                            }
                        }
                        $dataAllStates[] = $stateData;

                        if ($index == 0)
                            $labels = $weeks_name;
                    }
                } else if ($type == 'Hub') {
                    $stateCompanyData = array();
                    $stateIndividualData = array();
                    if ($date_from == '' && $date_to == '') {
                        foreach ($weeks as $value1) {
                            if ($index == 0) {
                                if ($hub_type == '') {
                                    $companyEachMonth = DB::select("SELECT count(collection.id) as total FROM collection left join collection_hub on collection.collection_hub_id = collection_hub.id left join customer on collection.customer_id = customer.id where customer.isIndividual = 0 and year(collection.created_at) = '$year' and collection.status = 1 and month(collection.created_at) = '$curr_mon' and day(collection.created_at) >= '$value1[0]' and day(collection.created_at) <= '$value1[1]'");

                                    $individualEachMonth = DB::select("SELECT count(collection.id)  as total FROM collection left join collection_hub on collection.collection_hub_id = collection_hub.id left join customer on collection.customer_id = customer.id where customer.isIndividual = 1 and year(collection.created_at) = '$year' and collection.status = 1 and month(collection.created_at) = '$curr_mon' and day(collection.created_at) >= '$value1[0]' and day(collection.created_at) <= '$value1[1]'");
                                } else {
                                    $companyEachMonth = DB::select("SELECT count(collection.id) as total FROM collection left join collection_hub on collection.collection_hub_id = collection_hub.id left join customer on collection.customer_id = customer.id where collection_hub.type = $hub_type and customer.isIndividual = 0 and year(collection.created_at) = '$year' and collection.status = 1 and month(collection.created_at) = '$curr_mon' and day(collection.created_at) >= '$value1[0]' and day(collection.created_at) <= '$value1[1]'");

                                    $individualEachMonth = DB::select("SELECT count(collection.id)  as total FROM collection left join collection_hub on collection.collection_hub_id = collection_hub.id left join customer on collection.customer_id = customer.id where collection_hub.type = $hub_type and customer.isIndividual = 1 and year(collection.created_at) = '$year' and collection.status = 1 and month(collection.created_at) = '$curr_mon' and day(collection.created_at) >= '$value1[0]' and day(collection.created_at) <= '$value1[1]'");
                                }
                                $companyMonth[] = array_column($companyEachMonth, 'total')[0];
                                $individualMonth[] = array_column($individualEachMonth, 'total')[0];
                            }
                            if ($hub_type == '') {
                                $companyTransactionMonthState = DB::select("SELECT count(collection.id) as total FROM collection left join collection_hub on collection.collection_hub_id = collection_hub.id left join customer on collection.customer_id = customer.id left join state on customer.state = state.id where customer.isIndividual = 0 and collection_hub.hub_state_id = ? and year(collection.created_at) = '$year' and collection.status = 1 and month(collection.created_at) = '$curr_mon' and day(collection.created_at) >= '$value1[0]' and day(collection.created_at) <= '$value1[1]'", array($value['id']));

                                $individualTransactionMonthState = DB::select("SELECT count(collection.id) as total FROM collection left join collection_hub on collection.collection_hub_id = collection_hub.id left join customer on collection.customer_id = customer.id left join state on customer.state = state.id where customer.isIndividual = 1 and collection_hub.hub_state_id = ? and year(collection.created_at) = '$year' and collection.status = 1 and month(collection.created_at) = '$curr_mon' and day(collection.created_at) >= '$value1[0]' and day(collection.created_at) <= '$value1[1]'", array($value['id']));
                            } else {
                                $companyTransactionMonthState = DB::select("SELECT count(collection.id) as total FROM collection left join collection_hub on collection.collection_hub_id = collection_hub.id left join customer on collection.customer_id = customer.id left join state on customer.state = state.id where collection_hub.type = $hub_type  and customer.isIndividual = 0 and collection_hub.hub_state_id = ? and year(collection.created_at) = '$year' and collection.status = 1 and month(collection.created_at) = '$curr_mon' and day(collection.created_at) >= '$value1[0]' and day(collection.created_at) <= '$value1[1]'", array($value['id']));

                                $individualTransactionMonthState = DB::select("SELECT count(collection.id) as total FROM collection left join collection_hub on collection.collection_hub_id = collection_hub.id left join customer on collection.customer_id = customer.id left join state on customer.state = state.id where collection_hub.type = $hub_type  and customer.isIndividual = 1 and collection_hub.hub_state_id = ? and year(collection.created_at) = '$year' and collection.status = 1 and month(collection.created_at) = '$curr_mon' and day(collection.created_at) >= '$value1[0]' and day(collection.created_at) <= '$value1[1]'", array($value['id']));
                            }
                            $stateCompanyData[] = array_column($companyTransactionMonthState, 'total')[0];
                            $stateIndividualData[] = array_column($individualTransactionMonthState, 'total')[0];
                        }
                        $dataAllStatesCompany[] = $stateCompanyData;
                        $dataAllStatesIndividual[] = $stateIndividualData;
                        $labels = $weeksLabel;
                    } else {
                        $weeks_name = array();
                        $stateData = array();

                        foreach ($period as $key => $dt) {
                            $break = false;
                            $curr_year = $dt->format('Y');
                            $curr_mon = $dt->format('m');
                            foreach ($weeks as $key1 => $value1) {
                                if ($break == true)
                                    break;
                                if ($key == 0) {
                                    if ($dayOfStartDate >= $value1[0] && $dayOfStartDate <= $value1[1] == false) {
                                        continue;
                                    }
                                }
                                if ($monthOfEndDate == $curr_mon) {
                                    if ($dayOfEndDate >= $value1[0] && $dayOfEndDate <= $value1[1]) {
                                        $break = true;
                                    }
                                }

                                if ($index == 0) {
                                    array_push($weeks_name, $dt->format('Y M ') . $weeksLabel[$key1]);

                                    if ($hub_type == '') {
                                        $companyEachMonth = DB::select("SELECT count(collection.id) as total FROM collection left join collection_hub on collection.collection_hub_id = collection_hub.id left join customer on collection.customer_id = customer.id where customer.isIndividual = 0 and year(collection.created_at) = '$curr_year' and collection.status = 1 and month(collection.created_at) = '$curr_mon' and day(collection.created_at) >= '$value1[0]' and day(collection.created_at) <= '$value1[1]'");

                                        $individualEachMonth = DB::select("SELECT count(collection.id)  as total FROM collection left join collection_hub on collection.collection_hub_id = collection_hub.id left join customer on collection.customer_id = customer.id where customer.isIndividual = 1 and year(collection.created_at) = '$curr_year' and collection.status = 1 and month(collection.created_at) = '$curr_mon' and day(collection.created_at) >= '$value1[0]' and day(collection.created_at) <= '$value1[1]'");
                                    } else {
                                        $companyEachMonth = DB::select("SELECT count(collection.id) as total FROM collection left join collection_hub on collection.collection_hub_id = collection_hub.id left join customer on collection.customer_id = customer.id where collection_hub.type = $hub_type and customer.isIndividual = 0 and year(collection.created_at) = '$curr_year' and collection.status = 1 and month(collection.created_at) = '$curr_mon' and day(collection.created_at) >= '$value1[0]' and day(collection.created_at) <= '$value1[1]'");

                                        $individualEachMonth = DB::select("SELECT count(collection.id)  as total FROM collection left join collection_hub on collection.collection_hub_id = collection_hub.id left join customer on collection.customer_id = customer.id where collection_hub.type = $hub_type and customer.isIndividual = 1 and year(collection.created_at) = '$curr_year' and collection.status = 1 and month(collection.created_at) = '$curr_mon' and day(collection.created_at) >= '$value1[0]' and day(collection.created_at) <= '$value1[1]'");
                                    }
                                    $companyMonth[] = array_column($companyEachMonth, 'total')[0];
                                    $individualMonth[] = array_column($individualEachMonth, 'total')[0];
                                }
                                if ($hub_type == '') {
                                    $companyTransactionMonthState = DB::select("SELECT count(collection.id) as total FROM collection left join collection_hub on collection.collection_hub_id = collection_hub.id left join customer on collection.customer_id = customer.id left join state on customer.state = state.id where customer.isIndividual = 0 and collection_hub.hub_state_id = ? and year(collection.created_at) = '$curr_year' and collection.status = 1 and month(collection.created_at) = '$curr_mon' and day(collection.created_at) >= '$value1[0]' and day(collection.created_at) <= '$value1[1]'", array($value['id']));

                                    $individualTransactionMonthState = DB::select("SELECT count(collection.id) as total FROM collection left join collection_hub on collection.collection_hub_id = collection_hub.id left join customer on collection.customer_id = customer.id left join state on customer.state = state.id where customer.isIndividual = 1 and collection_hub.hub_state_id = ? and year(collection.created_at) = '$curr_year' and collection.status = 1 and month(collection.created_at) = '$curr_mon' and day(collection.created_at) >= '$value1[0]' and day(collection.created_at) <= '$value1[1]'", array($value['id']));
                                } else {
                                    $companyTransactionMonthState = DB::select("SELECT count(collection.id) as total FROM collection left join collection_hub on collection.collection_hub_id = collection_hub.id left join customer on collection.customer_id = customer.id left join state on customer.state = state.id where collection_hub.type = $hub_type  and customer.isIndividual = 0 and collection_hub.hub_state_id = ? and year(collection.created_at) = '$curr_year' and collection.status = 1 and month(collection.created_at) = '$curr_mon' and day(collection.created_at) >= '$value1[0]' and day(collection.created_at) <= '$value1[1]'", array($value['id']));

                                    $individualTransactionMonthState = DB::select("SELECT count(collection.id) as total FROM collection left join collection_hub on collection.collection_hub_id = collection_hub.id left join customer on collection.customer_id = customer.id left join state on customer.state = state.id where collection_hub.type = $hub_type  and customer.isIndividual = 1 and collection_hub.hub_state_id = ? and year(collection.created_at) = '$curr_year' and collection.status = 1 and month(collection.created_at) = '$curr_mon' and day(collection.created_at) >= '$value1[0]' and day(collection.created_at) <= '$value1[1]'", array($value['id']));
                                }

                                $stateCompanyData[] = array_column($companyTransactionMonthState, 'total')[0];
                                $stateIndividualData[] = array_column($individualTransactionMonthState, 'total')[0];
                            }
                        }
                        $dataAllStatesCompany[] = $stateCompanyData;
                        $dataAllStatesIndividual[] = $stateIndividualData;
                        if ($index == 0)
                            $labels = $weeks_name;
                    }
                }
                $allStatesName[] = $value['name'];
            }
        }
        if ($type == "User")
            return array($companyMonth, $individualMonth, $userMonth, $labels, $dataAllStates, $allStatesName, $colors, $tableData);
        if ($type == "Hub")
            return array($companyMonth, $individualMonth, $labels, $dataAllStatesCompany, $allStatesName, $colors, $dataAllStatesIndividual, $tableData);
    }

    public function get_collection_collected_transaction_district(Request $request)
    {
        $hub_type = $request->hub_type;
        $type = $request->type;
        $format = $request->format;
        $state_id = $request->state_id;
        $months = Helper::monthInThisYear();
        $year = date("Y");
        $weeks = Helper::dayInWeek();
        $weeksLabel = Helper::weekLabel();
        $curr_mon = date('m');
        $hubs = CollectionHub::where('hub_state_id', $state_id)->where('type', $hub_type)->get()->toArray();
        $allCities = City::where('state_id', $state_id)->get()->toArray();
        $date_from = $request->date_from;
        $date_to = $request->date_to;

        if ($date_from == '' && $date_to == '') {
            if ($type == "User") {
                foreach ($allCities as $index => $value) {
                    $dataMonth = array();
                    if ($format == 'Month') {
                        foreach ($months[0] as $value1) {
                            $transactionMonthCities = DB::select("SELECT count(collection.id) as total FROM collection left join collection_hub on collection.collection_hub_id = collection_hub.id left join customer on collection.customer_id = customer.id left join state on customer.state = state.id where collection_hub.type = $hub_type and customer.city = ? and YEAR(collection.created_at) =  YEAR(CURRENT_DATE()) and collection.status = 1 and month(collection.created_at) = '$value1'", array($value['id']));

                            if ($index == 0) {
                                $transactionMonthAllCities = DB::select("SELECT count(collection.id) as total FROM collection left join collection_hub on collection.collection_hub_id = collection_hub.id left join customer on collection.customer_id = customer.id left join state on customer.state = state.id where collection_hub.type = $hub_type and customer.state = ? and YEAR(collection.created_at) =  YEAR(CURRENT_DATE()) and collection.status = 1 and month(collection.created_at) = '$value1'", array($state_id));
                                $dataAll[] = array_column($transactionMonthAllCities, 'total')[0];
                            }
                            $dataMonth[] = array_column($transactionMonthCities, 'total')[0];
                        }
                        $cityData[] = $dataMonth;
                        $city[] = $value['name'];
                    }
                    if ($format == 'Week') {
                        foreach ($weeks as $value1) {
                            $transactionMonthCities = DB::select("SELECT count(collection.id) as total FROM collection left join collection_hub on collection.collection_hub_id = collection_hub.id left join customer on collection.customer_id = customer.id left join state on customer.state = state.id where collection_hub.type = $hub_type and customer.city = ? and YEAR(collection.created_at) =  YEAR(CURRENT_DATE()) and collection.status = 1 and month(collection.created_at) = '$curr_mon' and day(collection.created_at) >= '$value1[0]' and day(collection.created_at) <= '$value1[1]'", array($value['id']));

                            if ($index == 0) {
                                $transactionMonthAllCities = DB::select("SELECT count(collection.id) as total FROM collection left join collection_hub on collection.collection_hub_id = collection_hub.id left join customer on collection.customer_id = customer.id left join state on customer.state = state.id where collection_hub.type = $hub_type and customer.state = ? and YEAR(collection.created_at) =  YEAR(CURRENT_DATE()) and month(collection.created_at) = '$curr_mon' and collection.status = 1 and day(collection.created_at) >= '$value1[0]' and day(collection.created_at) <= '$value1[1]'", array($state_id));
                                $dataAll[] = array_column($transactionMonthAllCities, 'total')[0];
                            }
                            $dataMonth[] = array_column($transactionMonthCities, 'total')[0];
                        }
                        $cityData[] = $dataMonth;
                        $city[] = $value['name'];
                    }
                }
            }

            if ($type == "Hub") {
                $dataCompany = array();
                $dataIndividual = array();

                foreach ($hubs as $value) {
                    $hub_id = $value['id'];
                    $companyMonth = array();
                    $individualMonth = array();
                    if ($format == "Month") {
                        foreach ($months[0] as $value1) {
                            $companyTransactionMonthCities = DB::select("SELECT count(collection.id) as total FROM collection left join collection_hub on collection.collection_hub_id = collection_hub.id left join customer on collection.customer_id = customer.id left join state on customer.state = state.id where collection_hub.type = $hub_type   and customer.isIndividual = 0 and collection.collection_hub_id = $hub_id and YEAR(collection.created_at) =  YEAR(CURRENT_DATE()) and collection.status = 1 and month(collection.created_at) = '$value1'", array($value['id']));

                            $individualTransactionMonthCities = DB::select("SELECT count(collection.id) as total FROM collection left join collection_hub on collection.collection_hub_id = collection_hub.id left join customer on collection.customer_id = customer.id left join state on customer.state = state.id where collection_hub.type = $hub_type   and customer.isIndividual = 1 and collection.collection_hub_id = $hub_id and YEAR(collection.created_at) =  YEAR(CURRENT_DATE()) and collection.status = 1 and month(collection.created_at) = '$value1'", array($value['id']));

                            $companyMonth[] = array_column($companyTransactionMonthCities, 'total')[0];
                            $individualMonth[] = array_column($individualTransactionMonthCities, 'total')[0];
                        }
                        $dataCompany[] = $companyMonth;
                        $dataIndividual[] = $individualMonth;
                    }
                    if ($format == "Week") {
                        foreach ($weeks as $value1) {
                            $companyTransactionMonthCities = DB::select("SELECT count(collection.id) as total FROM collection left join collection_hub on collection.collection_hub_id = collection_hub.id left join customer on collection.customer_id = customer.id left join state on customer.state = state.id where collection_hub.type = $hub_type   and customer.isIndividual = 0 and collection.collection_hub_id = $hub_id and YEAR(collection.created_at) =  YEAR(CURRENT_DATE()) and collection.status = 1 and month(collection.created_at) = '$curr_mon' and day(collection.created_at) >= '$value1[0]' and day(collection.created_at) <= '$value1[1]'", array($value['id']));

                            $individualTransactionMonthCities = DB::select("SELECT count(collection.id) as total FROM collection left join collection_hub on collection.collection_hub_id = collection_hub.id left join customer on collection.customer_id = customer.id left join state on customer.state = state.id where collection_hub.type = $hub_type   and customer.isIndividual = 1 and collection.collection_hub_id = $hub_id and YEAR(collection.created_at) =  YEAR(CURRENT_DATE()) and collection.status = 1 and month(collection.created_at) = '$curr_mon' and day(collection.created_at) >= '$value1[0]' and day(collection.created_at) <= '$value1[1]'", array($value['id']));

                            $companyMonth[] = array_column($companyTransactionMonthCities, 'total')[0];
                            $individualMonth[] = array_column($individualTransactionMonthCities, 'total')[0];
                        }
                        $dataCompany[] = $companyMonth;
                        $dataIndividual[] = $individualMonth;
                    }
                }
            }
            if ($format == "Month")
                $labels = $months[1];
            if ($format == "Week")
                $labels = $weeksLabel;
        } else {
            $labels = array();
            $start    = (new DateTime($date_from))->modify('first day of this month');
            $end      = (new DateTime($date_to))->modify('first day of next month');
            $interval = DateInterval::createFromDateString('1 month');
            $period   = new DatePeriod($start, $interval, $end);
            $dayOfStartDate = date('d', strtotime($date_from));
            $dayOfEndDate = date('d', strtotime($date_to));
            $monthOfEndDate = date('m', strtotime($date_to));

            if ($type == "User") {
                foreach ($allCities as $index => $value) {
                    $dataMonth = array();
                    if ($format == 'Month') {
                        foreach ($period as $key => $dt) {
                            $year = $dt->format('Y');
                            $month = $dt->format('m');

                            $transactionMonthCities = DB::select("SELECT count(collection.id) as total FROM collection left join collection_hub on collection.collection_hub_id = collection_hub.id left join customer on collection.customer_id = customer.id left join state on customer.state = state.id where collection_hub.type = $hub_type and customer.city = ? and collection.created_at >= '$date_from' and collection.created_at <= '$date_to' and month(collection.created_at) = $month and collection.status = 1 and year(collection.created_at) = $year", array($value['id']));

                            if ($index == 0) {
                                array_push($labels, $dt->format('M y'));

                                $transactionMonthAllCities = DB::select("SELECT count(collection.id) as total FROM collection left join collection_hub on collection.collection_hub_id = collection_hub.id left join customer on collection.customer_id = customer.id left join state on customer.state = state.id where collection_hub.type = $hub_type and customer.state = ? and collection.created_at >= '$date_from' and collection.created_at <= '$date_to' and month(collection.created_at) = $month and collection.status = 1 and year(collection.created_at) = $year", array($state_id));

                                $dataAll[] = array_column($transactionMonthAllCities, 'total')[0];
                            }
                            $dataMonth[] = array_column($transactionMonthCities, 'total')[0];
                        }
                        $cityData[] = $dataMonth;
                        $city[] = $value['name'];
                    }
                    if ($format == 'Week') {
                        foreach ($period as $key => $dt) {
                            $break = false;
                            $curr_year = $dt->format('Y');
                            $curr_mon = $dt->format('m');
                            foreach ($weeks as $key1 => $value1) {
                                if ($break == true)
                                    break;
                                if ($key == 0) {
                                    if ($dayOfStartDate >= $value1[0] && $dayOfStartDate <= $value1[1] == false) {
                                        continue;
                                    }
                                }
                                if ($monthOfEndDate == $curr_mon) {
                                    if ($dayOfEndDate >= $value1[0] && $dayOfEndDate <= $value1[1]) {
                                        $break = true;
                                    }
                                }

                                $transactionMonthCities = DB::select("SELECT count(collection.id) as total FROM collection left join collection_hub on collection.collection_hub_id = collection_hub.id left join customer on collection.customer_id = customer.id left join state on customer.state = state.id where collection_hub.type = $hub_type and customer.city = ?  and month(collection.created_at) = $curr_mon and year(collection.created_at) = $curr_year and day(collection.created_at) >= $value1[0] and collection.status = 1 and day(collection.created_at) <= $value1[1]", array($value['id']));

                                if ($index == 0) {
                                    array_push($labels,  $dt->format('Y M ') . $weeksLabel[$key1]);

                                    $transactionMonthAllCities = DB::select("SELECT count(collection.id) as total FROM collection left join collection_hub on collection.collection_hub_id = collection_hub.id left join customer on collection.customer_id = customer.id left join state on customer.state = state.id where collection_hub.type = $hub_type and customer.state = ?  and month(collection.created_at) = $curr_mon and year(collection.created_at) = $curr_year and day(collection.created_at) >= $value1[0] and collection.status = 1 and day(collection.created_at) <= $value1[1]", array($state_id));
                                    $dataAll[] = array_column($transactionMonthAllCities, 'total')[0];
                                }
                                $dataMonth[] = array_column($transactionMonthCities, 'total')[0];
                            }
                        }
                        $city[] = $value['name'];
                        $cityData[] = $dataMonth;
                    }
                }
            }

            if ($type == "Hub") {
                $dataCompany = array();
                $dataIndividual = array();

                foreach ($hubs as $hubIndex => $value) {
                    $hub_id = $value['id'];
                    $companyMonth = array();
                    $individualMonth = array();
                    if ($format == "Month") {

                        foreach ($period as $key => $dt) {
                            $year = $dt->format('Y');
                            $month = $dt->format('m');

                            if ($hubIndex == 0) {
                                array_push($labels, $dt->format('M y'));
                            }

                            $companyTransactionMonthCities = DB::select("SELECT count(collection.id) as total FROM collection left join collection_hub on collection.collection_hub_id = collection_hub.id left join customer on collection.customer_id = customer.id left join state on customer.state = state.id where collection_hub.type = $hub_type   and customer.isIndividual = 0 and collection.collection_hub_id = $hub_id and collection.created_at >= '$date_from' and collection.created_at <= '$date_to' and month(collection.created_at) = $month and collection.status = 1 and year(collection.created_at) = $year", array($value['id']));

                            $individualTransactionMonthCities = DB::select("SELECT count(collection.id) as total FROM collection left join collection_hub on collection.collection_hub_id = collection_hub.id left join customer on collection.customer_id = customer.id left join state on customer.state = state.id where collection_hub.type = $hub_type   and customer.isIndividual = 1 and collection.collection_hub_id = $hub_id and collection.created_at >= '$date_from' and collection.created_at <= '$date_to' and month(collection.created_at) = $month and collection.status = 1 and year(collection.created_at) = $year", array($value['id']));

                            $companyMonth[] = array_column($companyTransactionMonthCities, 'total')[0];
                            $individualMonth[] = array_column($individualTransactionMonthCities, 'total')[0];
                        }
                        $dataCompany[] = $companyMonth;
                        $dataIndividual[] = $individualMonth;
                    }
                    if ($format == "Week") {
                        foreach ($period as $key => $dt) {
                            $break = false;
                            $curr_year = $dt->format('Y');
                            $curr_mon = $dt->format('m');
                            foreach ($weeks as $key1 => $value1) {
                                if ($break == true)
                                    break;
                                if ($key == 0) {
                                    if ($dayOfStartDate >= $value1[0] && $dayOfStartDate <= $value1[1] == false) {
                                        continue;
                                    }
                                }
                                if ($monthOfEndDate == $curr_mon) {
                                    if ($dayOfEndDate >= $value1[0] && $dayOfEndDate <= $value1[1]) {
                                        $break = true;
                                    }
                                }
                                if ($hubIndex == 0)
                                    array_push($labels, $dt->format('Y M ') . $weeksLabel[$key1]);
                                $companyTransactionMonthCities = DB::select("SELECT count(collection.id) as total FROM collection left join collection_hub on collection.collection_hub_id = collection_hub.id left join customer on collection.customer_id = customer.id left join state on customer.state = state.id where collection_hub.type = $hub_type   and customer.isIndividual = 0 and collection.collection_hub_id = $hub_id and month(collection.created_at) = $curr_mon and year(collection.created_at) = $curr_year and collection.status = 1 and day(collection.created_at) >= $value1[0] and day(collection.created_at) <= $value1[1]", array($value['id']));

                                $individualTransactionMonthCities = DB::select("SELECT count(collection.id) as total FROM collection left join collection_hub on collection.collection_hub_id = collection_hub.id left join customer on collection.customer_id = customer.id left join state on customer.state = state.id where collection_hub.type = $hub_type   and customer.isIndividual = 1 and collection.collection_hub_id = $hub_id and YEAR(collection.created_at) =  YEAR(CURRENT_DATE()) and month(collection.created_at) = '$curr_mon' and collection.status = 1 and day(collection.created_at) >= '$value1[0]' and day(collection.created_at) <= '$value1[1]'", array($value['id']));

                                $companyMonth[] = array_column($companyTransactionMonthCities, 'total')[0];
                                $individualMonth[] = array_column($individualTransactionMonthCities, 'total')[0];
                            }
                        }
                        $dataCompany[] = $companyMonth;
                        $dataIndividual[] = $individualMonth;
                    }
                }
            }
        }

        if ($type == "User") {
            $colors = Helper::randomColor($allCities);
            $array = array($city, $colors, $cityData, $labels, $dataAll);
        }
        if ($type == "Hub") {
            $colors = Helper::randomColor($hubs);
            $color = Helper::randomColor($hubs);
            $colors = array_merge($colors, $color);
            $array = array($hubs, $colors, $dataCompany, $labels, $dataIndividual);
        }
        return $array;
    }

    public function get_collection_collected_waste_data(Request $request)
    {
        $hub_type = $request->hub_type;
        $curr_mon = date('m');
        $curr_year = date('Y');
        $format = $request->format;
        $months = Helper::monthInThisYear();
        $weeks = Helper::dayInWeek();
        $weeksLabel = Helper::weekLabel();
        $allStates = State::get()->toArray();
        $colors = Helper::randomColor($allStates);
        $allStatesName = array();
        $labels = array();
        $categoryByMonth = array();
        $categoryByMonthTotal = array();
        $categoryByWeek = array();
        $categoryByWeekTotal = array();
        $categoryLabel = RecycleCategory::orderBy('id')->get()->toArray();
        $date_from = $request->date_from;
        $date_to = $request->date_to;

        if ($date_from == '' && $date_to == '') {
            if ($format == 'Month') {
                foreach ($months[0] as  $index => $value) {
                    if ($hub_type == '') {
                        $categoryEachMonth = DB::select("SELECT round(ifnull(sum(B.weight),0),2) as total, rc.name as name FROM (SELECT cd.weight,rt.recycle_category_id from collection c left join collection_detail cd on c.id = cd.collection_id left join recycle_type rt on cd.recycling_type_id = rt.id left join collection_hub chb on chb.id = c.collection_hub_id where month(c.created_at) = $value and c.status = 1  and year(c.created_at) = $curr_year and c.status = 1) AS B RIGHT JOIN recycle_category rc on rc.id = B.recycle_category_id group by rc.id,rc.name order by rc.id");

                        $categoryEachMonthTotal = DB::select("SELECT round(ifnull(sum(cd.weight),0),2) as total from collection c left join collection_detail cd on c.id = cd.collection_id left join recycle_type rt on cd.recycling_type_id = rt.id left join collection_hub chb on chb.id = c.collection_hub_id  where month(c.created_at) = $value and c.status = 1 and year(c.created_at) = $curr_year and c.status = 1");
                    } else {
                        $categoryEachMonth = DB::select("SELECT round(ifnull(sum(B.weight),0),2) as total, rc.name as name FROM (SELECT cd.weight,rt.recycle_category_id from collection c left join collection_detail cd on c.id = cd.collection_id left join recycle_type rt on cd.recycling_type_id = rt.id left join collection_hub chb on chb.id = c.collection_hub_id where month(c.created_at) = $value and c.status = 1 and year(c.created_at) = $curr_year and chb.type = $hub_type and c.status = 1 ) AS B RIGHT JOIN recycle_category rc on rc.id = B.recycle_category_id group by rc.id,rc.name order by rc.id");

                        $categoryEachMonthTotal = DB::select("SELECT round(ifnull(sum(cd.weight),0),2) as total from collection c left join collection_detail cd on c.id = cd.collection_id left join recycle_type rt on cd.recycling_type_id = rt.id left join collection_hub chb on chb.id = c.collection_hub_id  where month(c.created_at) = $value and c.status = 1 and year(c.created_at) = $curr_year and chb.type = $hub_type and c.status = 1");
                    }
                    $categoryEachMonth = array_column($categoryEachMonth, 'total');
                    $categoryEachMonthTotal = array_column($categoryEachMonthTotal, 'total')[0];
                    array_push($categoryByMonth, $categoryEachMonth);
                    array_push($categoryByMonthTotal, $categoryEachMonthTotal);
                }
                $labels = $months[1];
            }

            if ($format == 'Week') {
                foreach ($weeks as $value) {
                    if ($hub_type == '') {
                        $categoryEachWeek = DB::select("SELECT round(ifnull(sum(B.weight),0),2) as total , rc.name as name FROM (SELECT cd.weight,rt.recycle_category_id from collection c left join collection_detail cd on c.id = cd.collection_id left join recycle_type rt on cd.recycling_type_id = rt.id left join collection_hub chb on chb.id = c.collection_hub_id where month(c.created_at) = $curr_mon and c.status = 1 and year(c.created_at) = $curr_year and day(c.created_at) >= $value[0] and day(c.created_at) <= $value[1] and c.status = 1) AS B RIGHT JOIN recycle_category rc on rc.id = B.recycle_category_id group by rc.id,rc.name order by rc.id");

                        $categoryEachWeekTotal = DB::select("SELECT round(ifnull(sum(cd.weight),0),2) as total from collection c left join collection_detail cd on c.id = cd.collection_id left join recycle_type rt on cd.recycling_type_id = rt.id left join collection_hub chb on chb.id = c.collection_hub_id where month(c.created_at) = $curr_mon and c.status = 1 and year(c.created_at) = $curr_year and day(c.created_at) >= $value[0] and day(c.created_at) <= $value[1] and c.status = 1");
                    } else {
                        $categoryEachWeek = DB::select("SELECT round(ifnull(sum(B.weight),0),2) as total , rc.name as name FROM (SELECT cd.weight,rt.recycle_category_id from collection c left join collection_detail cd on c.id = cd.collection_id left join recycle_type rt on cd.recycling_type_id = rt.id left join collection_hub chb on chb.id = c.collection_hub_id where month(c.created_at) = $curr_mon and c.status = 1 and year(c.created_at) = $curr_year and day(c.created_at) >= $value[0] and day(c.created_at) <= $value[1] and chb.type = $hub_type and c.status = 1) AS B RIGHT JOIN recycle_category rc on rc.id = B.recycle_category_id group by rc.id,rc.name order by rc.id");

                        $categoryEachWeekTotal = DB::select("SELECT round(ifnull(sum(cd.weight),0),2) as total from collection c left join collection_detail cd on c.id = cd.collection_id left join recycle_type rt on cd.recycling_type_id = rt.id left join collection_hub chb on chb.id = c.collection_hub_id where month(c.created_at) = $curr_mon and c.status = 1 and year(c.created_at) = $curr_year and day(c.created_at) >= $value[0] and day(c.created_at) <= $value[1] and chb.type = $hub_type and c.status = 1");
                    }

                    $categoryEachWeek = array_column($categoryEachWeek, 'total');
                    $categoryEachWeekTotal = array_column($categoryEachWeekTotal, 'total')[0];
                    array_push($categoryByWeek, $categoryEachWeek);
                    array_push($categoryByWeekTotal, $categoryEachWeekTotal);
                }
                $labels = $weeksLabel;
            }
        } else {
            $start    = (new DateTime($date_from))->modify('first day of this month');
            $end      = (new DateTime($date_to))->modify('first day of next month');
            $interval = DateInterval::createFromDateString('1 month');
            $period   = new DatePeriod($start, $interval, $end);
            $dayOfStartDate = date('d', strtotime($date_from));
            $dayOfEndDate = date('d', strtotime($date_to));
            $monthOfEndDate = date('m', strtotime($date_to));
            if ($format == 'Month') {
                foreach ($period as $key => $dt) {
                    $year = $dt->format('Y');
                    $month = $dt->format('m');
                    array_push($labels, $dt->format('M y'));
                    if ($hub_type == '') {
                        $categoryEachMonth = DB::select("SELECT round(ifnull(sum(B.weight),0),2) as total, rc.name as name FROM (SELECT cd.weight,rt.recycle_category_id from collection c left join collection_detail cd on c.id = cd.collection_id left join recycle_type rt on cd.recycling_type_id = rt.id left join collection_hub chb on chb.id = c.collection_hub_id where c.created_at >= '$date_from' and c.status = 1 and c.created_at <= '$date_to' and month(c.created_at) = $month and year(c.created_at) = $year and c.status = 1) AS B RIGHT JOIN recycle_category rc on rc.id = B.recycle_category_id group by rc.id,rc.name order by rc.id");

                        $categoryEachMonthTotal = DB::select("SELECT round(ifnull(sum(cd.weight),0),2) as total from collection c left join collection_detail cd on c.id = cd.collection_id left join recycle_type rt on cd.recycling_type_id = rt.id left join collection_hub chb on chb.id = c.collection_hub_id  where c.created_at >= '$date_from' and c.created_at <= '$date_to' and month(c.created_at) = $month and c.status = 1 and year(c.created_at) = $year and c.status = 1");
                    } else {
                        $categoryEachMonth = DB::select("SELECT round(ifnull(sum(B.weight),0),2) as total, rc.name as name FROM (SELECT cd.weight,rt.recycle_category_id from collection c left join collection_detail cd on c.id = cd.collection_id left join recycle_type rt on cd.recycling_type_id = rt.id left join collection_hub chb on chb.id = c.collection_hub_id where c.created_at >= '$date_from' and c.status = 1 and c.created_at <= '$date_to' and month(c.created_at) = $month and year(c.created_at) = $year and chb.type = $hub_type and c.status = 1) AS B RIGHT JOIN recycle_category rc on rc.id = B.recycle_category_id group by rc.id,rc.name order by rc.id");

                        $categoryEachMonthTotal = DB::select("SELECT round(ifnull(sum(cd.weight),0),2) as total from collection c left join collection_detail cd on c.id = cd.collection_id left join recycle_type rt on cd.recycling_type_id = rt.id left join collection_hub chb on chb.id = c.collection_hub_id  where c.created_at >= '$date_from' and c.created_at <= '$date_to' and month(c.created_at) = $month and c.status = 1 and year(c.created_at) = $year and chb.type = $hub_type and c.status = 1");
                    }

                    $categoryEachMonth = array_column($categoryEachMonth, 'total');
                    $categoryEachMonthTotal = array_column($categoryEachMonthTotal, 'total')[0];
                    array_push($categoryByMonth, $categoryEachMonth);
                    array_push($categoryByMonthTotal, $categoryEachMonthTotal);
                }
            }

            if ($format == 'Week') {
                foreach ($period as $key => $dt) {
                    $break = false;
                    $curr_year = $dt->format('Y');
                    $curr_mon = $dt->format('m');
                    foreach ($weeks as $key1 => $value) {
                        if ($break == true)
                            break;
                        if ($key == 0) {
                            if ($dayOfStartDate >= $value[0] && $dayOfStartDate <= $value[1] == false) {
                                continue;
                            }
                        }
                        if ($monthOfEndDate == $curr_mon) {
                            if ($dayOfEndDate >= $value[0] && $dayOfEndDate <= $value[1]) {
                                $break = true;
                            }
                        }
                        array_push($labels, $dt->format('Y M ') . $weeksLabel[$key1]);
                        if ($hub_type == '') {
                            $categoryEachWeek = DB::select("SELECT round(ifnull(sum(B.weight),0),2) as total , rc.name as name FROM (SELECT cd.weight,rt.recycle_category_id from collection c left join collection_detail cd on c.id = cd.collection_id left join recycle_type rt on cd.recycling_type_id = rt.id left join collection_hub chb on chb.id = c.collection_hub_id where month(c.created_at) = $curr_mon and c.status = 1 and year(c.created_at) = $curr_year and day(c.created_at) >= $value[0] and day(c.created_at) <= $value[1] and c.status = 1) AS B RIGHT JOIN recycle_category rc on rc.id = B.recycle_category_id group by rc.id,rc.name order by rc.id");

                            $categoryEachWeekTotal = DB::select("SELECT round(ifnull(sum(cd.weight),0),2) as total from collection c left join collection_detail cd on c.id = cd.collection_id left join recycle_type rt on cd.recycling_type_id = rt.id left join collection_hub chb on chb.id = c.collection_hub_id where month(c.created_at) = $curr_mon and year(c.created_at) = $curr_year and c.status = 1 and day(c.created_at) >= $value[0] and day(c.created_at) <= $value[1] and c.status = 1");
                        } else {
                            $categoryEachWeek = DB::select("SELECT round(ifnull(sum(B.weight),0),2) as total , rc.name as name FROM (SELECT cd.weight,rt.recycle_category_id from collection c left join collection_detail cd on c.id = cd.collection_id left join recycle_type rt on cd.recycling_type_id = rt.id left join collection_hub chb on chb.id = c.collection_hub_id where month(c.created_at) = $curr_mon and c.status = 1 and year(c.created_at) = $curr_year and day(c.created_at) >= $value[0] and day(c.created_at) <= $value[1] and chb.type = $hub_type and c.status = 1) AS B RIGHT JOIN recycle_category rc on rc.id = B.recycle_category_id group by rc.id,rc.name order by rc.id");

                            $categoryEachWeekTotal = DB::select("SELECT round(ifnull(sum(cd.weight),0),2) as total from collection c left join collection_detail cd on c.id = cd.collection_id left join recycle_type rt on cd.recycling_type_id = rt.id left join collection_hub chb on chb.id = c.collection_hub_id where month(c.created_at) = $curr_mon and c.status = 1 and year(c.created_at) = $curr_year and day(c.created_at) >= $value[0] and day(c.created_at) <= $value[1] and chb.type = $hub_type and c.status = 1");
                        }

                        $categoryEachWeek = array_column($categoryEachWeek, 'total');
                        $categoryEachWeekTotal = array_column($categoryEachWeekTotal, 'total')[0];
                        array_push($categoryByWeek, $categoryEachWeek);
                        array_push($categoryByWeekTotal, $categoryEachWeekTotal);
                    }
                }
            }
        }
        $categoryColor = Helper::randomColor($categoryLabel);
        $totalColor = Helper::randomColor(1);

        if ($format == 'Month') {
            return array($labels, $categoryByMonth, $categoryLabel, $categoryColor, $categoryByMonthTotal, $totalColor);
        }

        if ($format == 'Week') {
            return array($labels, $categoryByWeek, $categoryLabel, $categoryColor, $categoryByWeekTotal, $totalColor);
        }
    }

    public function get_collection_collected_waste_district(Request $request)
    {
        $hub_type = $request->hub_type;
        $state_id = $request->state_id;
        $format = $request->format;
        $curr_mon = date('m');
        $curr_year = date('Y');

        $months = Helper::monthInThisYear();
        $weeks = Helper::dayInWeek();
        $weeksLabel = Helper::weekLabel();

        $category = RecycleCategory::orderBy('id')->get();
        $data = array();
        $dataTotal = array();
        $date_from = $request->date_from;
        $date_to = $request->date_to;
        $labels = array();

        if ($date_from == '' && $date_to == '') {
            if ($format == 'Month') {
                foreach ($category as $value) {
                    $categoryByMonth = array();
                    $categoryByMonthTotal = array();
                    foreach ($months[0] as $value1) {
                        $categoryEachMonth = DB::select("SELECT round(ifnull(sum(B.weight),0),2) as total, ch.hub_name as name FROM (SELECT c.id,cd.weight,c.collection_hub_id from collection c left join collection_detail cd on c.id = cd.collection_id left join recycle_type rt on cd.recycling_type_id = rt.id left join collection_hub chb on chb.id = c.collection_hub_id where month(c.created_at) = $value1 and c.status = 1 and year(c.created_at) = $curr_year and rt.recycle_category_id = $value->id and chb.hub_state_id = $state_id and chb.type = $hub_type and c.status = 1) AS B RIGHT JOIN collection_hub ch on ch.id = B.collection_hub_id where ch.hub_state_id = $state_id and ch.type = $hub_type group by ch.id,ch.hub_name order by ch.id");

                        $categoryEachMonthTotal = DB::select("SELECT round(ifnull(sum(cd.weight),0),2) as total from collection c left join collection_detail cd on c.id = cd.collection_id left join recycle_type rt on cd.recycling_type_id = rt.id left join collection_hub chb on chb.id = c.collection_hub_id where month(c.created_at) = $value1 and year(c.created_at) = $curr_year and c.status = 1 and rt.recycle_category_id =$value->id and chb.hub_state_id = $state_id and chb.type = $hub_type and c.status = 1");

                        $categoryEachMonth = array_column($categoryEachMonth, 'total');
                        $categoryEachMonthTotal = array_column($categoryEachMonthTotal, 'total')[0];

                        array_push($categoryByMonth, $categoryEachMonth);
                        array_push($categoryByMonthTotal, $categoryEachMonthTotal);
                    }
                    array_push($data, $categoryByMonth);
                    array_push($dataTotal, $categoryByMonthTotal);
                }
                $labels = $months[1];
            }
            if ($format == 'Week') {
                foreach ($category as $value) {
                    $categoryByWeek = array();
                    $categoryByWeekTotal = array();
                    foreach ($weeks as $value1) {
                        $categoryEachWeek = DB::select("SELECT round(ifnull(sum(B.weight),0),2) as total, ch.hub_name as name FROM (SELECT c.id,cd.weight,c.collection_hub_id from collection c left join collection_detail cd on c.id = cd.collection_id left join recycle_type rt on cd.recycling_type_id = rt.id left join collection_hub chb on chb.id = c.collection_hub_id where month(c.created_at) = $curr_mon and c.status = 1 and year(c.created_at) = $curr_year and day(c.created_at) >= $value1[0] and day(c.created_at) <= $value1[1] and rt.recycle_category_id = $value->id and chb.hub_state_id = $state_id and chb.type = $hub_type and c.status = 1) AS B RIGHT JOIN collection_hub ch on ch.id = B.collection_hub_id where ch.hub_state_id = $state_id and ch.type = $hub_type group by ch.id,ch.hub_name order by ch.id");

                        $categoryEachWeekTotal = DB::select("SELECT round(ifnull(sum(cd.weight),0),2) as total from collection c left join collection_detail cd on c.id = cd.collection_id left join recycle_type rt on cd.recycling_type_id = rt.id left join collection_hub chb on chb.id = c.collection_hub_id where month(c.created_at) = $curr_mon and c.status = 1 and year(c.created_at) = $curr_year and day(c.created_at) >= $value1[0] and day(c.created_at) <= $value1[1] and rt.recycle_category_id =$value->id and chb.hub_state_id = $state_id and chb.type = $hub_type and c.status = 1");

                        $categoryEachWeek = array_column($categoryEachWeek, 'total');
                        $categoryEachWeekTotal = array_column($categoryEachWeekTotal, 'total')[0];
                        array_push($categoryByWeek, $categoryEachWeek);
                        array_push($categoryByWeekTotal, $categoryEachWeekTotal);
                    }
                    array_push($data, $categoryByWeek);
                    array_push($dataTotal, $categoryByWeekTotal);
                }
                $labels = $weeksLabel;
            }
        } else {
            $start    = (new DateTime($date_from))->modify('first day of this month');
            $end      = (new DateTime($date_to))->modify('first day of next month');
            $interval = DateInterval::createFromDateString('1 month');
            $period   = new DatePeriod($start, $interval, $end);
            $dayOfStartDate = date('d', strtotime($date_from));
            $dayOfEndDate = date('d', strtotime($date_to));
            $monthOfEndDate = date('m', strtotime($date_to));

            if ($format == 'Month') {
                foreach ($category as $index => $value) {
                    $categoryByMonth = array();
                    $categoryByMonthTotal = array();
                    foreach ($period as $key => $dt) {
                        $year = $dt->format('Y');
                        $month = $dt->format('m');

                        if ($index == 0)
                            array_push($labels, $dt->format('M Y'));

                        $categoryEachMonth = DB::select("SELECT round(ifnull(sum(B.weight),0),2) as total, ch.hub_name as name FROM (SELECT c.id,cd.weight,c.collection_hub_id from collection c left join collection_detail cd on c.id = cd.collection_id left join recycle_type rt on cd.recycling_type_id = rt.id left join collection_hub chb on chb.id = c.collection_hub_id where c.created_at >= '$date_from' and c.status = 1 and c.created_at <= '$date_to' and month(c.created_at) = $month and year(c.created_at) = $year and rt.recycle_category_id = $value->id and chb.hub_state_id = $state_id and chb.type = $hub_type and c.status = 1) AS B RIGHT JOIN collection_hub ch on ch.id = B.collection_hub_id where ch.hub_state_id = $state_id and ch.type = $hub_type group by ch.id,ch.hub_name order by ch.id");

                        $categoryEachMonthTotal = DB::select("SELECT round(ifnull(sum(cd.weight),0),2) as total from collection c left join collection_detail cd on c.id = cd.collection_id left join recycle_type rt on cd.recycling_type_id = rt.id left join collection_hub chb on chb.id = c.collection_hub_id where c.created_at >= '$date_from' and c.created_at <= '$date_to' and month(c.created_at) = $month and c.status = 1 and year(c.created_at) = $year and rt.recycle_category_id =$value->id and chb.hub_state_id = $state_id and chb.type = $hub_type and c.status = 1");

                        $categoryEachMonth = array_column($categoryEachMonth, 'total');
                        $categoryEachMonthTotal = array_column($categoryEachMonthTotal, 'total')[0];

                        array_push($categoryByMonth, $categoryEachMonth);
                        array_push($categoryByMonthTotal, $categoryEachMonthTotal);
                    }
                    array_push($data, $categoryByMonth);
                    array_push($dataTotal, $categoryByMonthTotal);
                }
            }
            if ($format == 'Week') {
                foreach ($category as $index => $value) {
                    $categoryByWeek = array();
                    $categoryByWeekTotal = array();
                    foreach ($period as $key => $dt) {
                        $break = false;
                        $curr_year = $dt->format('Y');
                        $curr_mon = $dt->format('m');
                        foreach ($weeks as $key1 => $value1) {
                            if ($break == true)
                                break;
                            if ($key == 0) {
                                if ($dayOfStartDate >= $value1[0] && $dayOfStartDate <= $value1[1] == false) {
                                    continue;
                                }
                            }
                            if ($monthOfEndDate == $curr_mon) {
                                if ($dayOfEndDate >= $value1[0] && $dayOfEndDate <= $value1[1]) {
                                    $break = true;
                                }
                            }
                            if ($index == 0)
                                array_push($labels, $dt->format('Y M ') . $weeksLabel[$key1]);

                            $categoryEachWeek = DB::select("SELECT round(ifnull(sum(B.weight),0),2) as total, ch.hub_name as name FROM (SELECT c.id,cd.weight,c.collection_hub_id from collection c left join collection_detail cd on c.id = cd.collection_id left join recycle_type rt on cd.recycling_type_id = rt.id left join collection_hub chb on chb.id = c.collection_hub_id where month(c.created_at) = $curr_mon and c.status = 1 and year(c.created_at) = $curr_year and day(c.created_at) >= $value1[0] and day(c.created_at) <= $value1[1] and rt.recycle_category_id = $value->id and chb.hub_state_id = $state_id and chb.type = $hub_type and c.status = 1 ) AS B RIGHT JOIN collection_hub ch on ch.id = B.collection_hub_id where ch.hub_state_id = $state_id and ch.type = $hub_type group by ch.id,ch.hub_name order by ch.id");

                            $categoryEachWeekTotal = DB::select("SELECT round(ifnull(sum(cd.weight),0),2) as total from collection c left join collection_detail cd on c.id = cd.collection_id left join recycle_type rt on cd.recycling_type_id = rt.id left join collection_hub chb on chb.id = c.collection_hub_id where month(c.created_at) = $curr_mon and year(c.created_at) = $curr_year and c.status = 1 and day(c.created_at) >= $value1[0] and day(c.created_at) <= $value1[1] and rt.recycle_category_id =$value->id and chb.hub_state_id = $state_id and chb.type = $hub_type and c.status = 1");

                            $categoryEachWeek = array_column($categoryEachWeek, 'total');
                            $categoryEachWeekTotal = array_column($categoryEachWeekTotal, 'total')[0];
                            array_push($categoryByWeek, $categoryEachWeek);
                            array_push($categoryByWeekTotal, $categoryEachWeekTotal);
                        }
                    }
                    array_push($data, $categoryByWeek);
                    array_push($dataTotal, $categoryByWeekTotal);
                }
            }
        }
        $category = $category->toArray();
        $color = Helper::randomColor($category);
        $hubs = CollectionHub::where('hub_state_id', $state_id)->where('type', $hub_type)->get();
        $colorTotal = Helper::randomColor(1);


        if ($format == 'Month') {
            return array($color, $category, $data, $hubs, $labels, $dataTotal, $colorTotal);
        }
        if ($format == 'Week') {
            return array($color, $category, $data, $hubs, $labels, $dataTotal, $colorTotal);
        }
    }

    public function get_collection_waste_selling_data(Request $request)
    {
        $hub_type = $request->hub_type;
        $curr_mon = date('m');
        $curr_year = date('Y');
        $format = $request->format;
        $months = Helper::monthInThisYear();
        $weeks = Helper::dayInWeek();
        $weeksLabel = Helper::weekLabel();

        $collectedWaste = array();
        $soldWaste = array();
        $collectedWasteTotal = array();
        $soldWasteTotal = array();
        $date_from = $request->date_from;
        $date_to = $request->date_to;
        $labels = array();
        $dayOfStartDate = date('d', strtotime($date_from));
        $dayOfEndDate = date('d', strtotime($date_to));
        $monthOfEndDate = date('m', strtotime($date_to));

        if ($date_from == '' && $date_to == '') {
            if ($format == 'Month') {
                foreach ($months[0] as  $index => $value) {
                    if ($hub_type == '') {
                        $collectedWaste = DB::select("SELECT IFNULL(round(sum(cd.weight),2),0) as total from collection c left join collection_detail cd on c.id = cd.collection_id left join collection_hub chb on chb.id = c.collection_hub_id where month(c.created_at) = $value and c.status = 1 and year(c.created_at) = $curr_year  and status = 1");

                        $soldWaste = DB::select("SELECT IFNULL(round(sum(wci.weight),2),0) as total from waste_clearance_schedule wcs left join waste_clearance_item wci on wcs.id = wci.waste_clearance_schedule_id left join collection_hub chb on chb.id = wcs.collection_hub_id where month(wcs.collection_time) = $value and year(wcs.collection_time) = $curr_year and wcs.status = 2");
                    } else {
                        $collectedWaste = DB::select("SELECT IFNULL(round(sum(cd.weight),2),0) as total from collection c left join collection_detail cd on c.id = cd.collection_id left join collection_hub chb on chb.id = c.collection_hub_id where month(c.created_at) = $value and c.status = 1 and year(c.created_at) = $curr_year  and chb.type = $hub_type and status = 1");

                        $soldWaste = DB::select("SELECT IFNULL(round(sum(wci.weight),2),0) as total from waste_clearance_schedule wcs left join waste_clearance_item wci on wcs.id = wci.waste_clearance_schedule_id left join collection_hub chb on chb.id = wcs.collection_hub_id where month(wcs.collection_time) = $value and year(wcs.collection_time) = $curr_year and chb.type = $hub_type and wcs.status = 2");
                    }

                    $collectedWaste = array_column($collectedWaste, 'total')[0];
                    $soldWaste = array_column($soldWaste, 'total')[0];
                    array_push($collectedWasteTotal, $collectedWaste);
                    array_push($soldWasteTotal, $soldWaste);
                }
                $labels = $months[1];
            }

            if ($format == 'Week') {
                foreach ($weeks as $value) {
                    if ($hub_type == '') {
                        $collectedWaste = DB::select("SELECT IFNULL(round(sum(cd.weight),2),0) as total from collection c left join collection_detail cd on c.id = cd.collection_id left join collection_hub chb on chb.id = c.collection_hub_id where month(c.created_at) = $curr_mon and c.status = 1 and year(c.created_at) = $curr_year and day(c.created_at) >= $value[0] and day(c.created_at) <= $value[1] and status = 1");

                        $soldWaste = DB::select("SELECT IFNULL(round(sum(wci.weight),2),0) as total from waste_clearance_schedule wcs left join waste_clearance_item wci on wcs.id = wci.waste_clearance_schedule_id left join collection_hub chb on chb.id = wcs.collection_hub_id where month(wcs.collection_time) = $curr_mon and year(wcs.collection_time) = $curr_year and day(wcs.collection_time) >= $value[0] and day(wcs.collection_time) <= $value[1] and wcs.status = 2");
                    } else {
                        $collectedWaste = DB::select("SELECT IFNULL(round(sum(cd.weight),2),0) as total from collection c left join collection_detail cd on c.id = cd.collection_id left join collection_hub chb on chb.id = c.collection_hub_id where month(c.created_at) = $curr_mon and c.status = 1 and year(c.created_at) = $curr_year and day(c.created_at) >= $value[0] and day(c.created_at) <= $value[1] and chb.type = $hub_type and status = 1");

                        $soldWaste = DB::select("SELECT IFNULL(round(sum(wci.weight),2),0) as total from waste_clearance_schedule wcs left join waste_clearance_item wci on wcs.id = wci.waste_clearance_schedule_id left join collection_hub chb on chb.id = wcs.collection_hub_id where month(wcs.collection_time) = $curr_mon and year(wcs.collection_time) = $curr_year and day(wcs.collection_time) >= $value[0] and day(wcs.collection_time) <= $value[1] and chb.type = $hub_type and wcs.status = 2");
                    }

                    $collectedWaste = array_column($collectedWaste, 'total')[0];
                    $soldWaste = array_column($soldWaste, 'total')[0];
                    array_push($collectedWasteTotal, $collectedWaste);
                    array_push($soldWasteTotal, $soldWaste);
                }
                $labels = $weeksLabel;
            }
        } else {
            $start    = (new DateTime($date_from))->modify('first day of this month');
            $end      = (new DateTime($date_to))->modify('first day of next month');
            $interval = DateInterval::createFromDateString('1 month');
            $period   = new DatePeriod($start, $interval, $end);
            $dayOfStartDate = date('d', strtotime($date_from));
            $dayOfEndDate = date('d', strtotime($date_to));
            $monthOfEndDate = date('m', strtotime($date_to));

            if ($format == 'Month') {
                foreach ($period as $key => $dt) {
                    $year = $dt->format('Y');
                    $month = $dt->format('m');
                    array_push($labels, $dt->format('M Y'));
                    if ($hub_type == '') {
                        $collectedWaste = DB::select("SELECT IFNULL(round(sum(cd.weight),2),0) as total from collection c left join collection_detail cd on c.id = cd.collection_id left join collection_hub chb on chb.id = c.collection_hub_id where c.created_at >= '$date_from' and c.status = 1 and c.created_at <= '$date_to' and month(c.created_at) = $month and year(c.created_at) = $year and status = 1");

                        $soldWaste = DB::select("SELECT IFNULL(round(sum(wci.weight),2),0) as total from waste_clearance_schedule wcs left join waste_clearance_item wci on wcs.id = wci.waste_clearance_schedule_id left join collection_hub chb on chb.id = wcs.collection_hub_id where wcs.collection_time >= '$date_from' and wcs.collection_time <= '$date_to' and month(wcs.collection_time) = $month and year(wcs.collection_time) = $year and wcs.status = 2");
                    } else {
                        $collectedWaste = DB::select("SELECT IFNULL(round(sum(cd.weight),2),0) as total from collection c left join collection_detail cd on c.id = cd.collection_id left join collection_hub chb on chb.id = c.collection_hub_id where c.created_at >= '$date_from' and c.status = 1 and c.created_at <= '$date_to' and month(c.created_at) = $month and year(c.created_at) = $year  and chb.type = $hub_type and status = 1");

                        $soldWaste = DB::select("SELECT IFNULL(round(sum(wci.weight),2),0) as total from waste_clearance_schedule wcs left join waste_clearance_item wci on wcs.id = wci.waste_clearance_schedule_id left join collection_hub chb on chb.id = wcs.collection_hub_id where wcs.collection_time >= '$date_from' and wcs.collection_time <= '$date_to' and month(wcs.collection_time) = $month and year(wcs.collection_time) = $year and chb.type = $hub_type and wcs.status = 2");
                    }

                    $collectedWaste = array_column($collectedWaste, 'total')[0];
                    $soldWaste = array_column($soldWaste, 'total')[0];
                    array_push($collectedWasteTotal, $collectedWaste);
                    array_push($soldWasteTotal, $soldWaste);
                }
            }

            if ($format == 'Week') {
                foreach ($period as $key => $dt) {
                    $break = false;
                    $curr_year = $dt->format('Y');
                    $curr_mon = $dt->format('m');
                    foreach ($weeks as $key1 => $value1) {
                        if ($break == true)
                            break;
                        if ($key == 0) {
                            if ($dayOfStartDate >= $value1[0] && $dayOfStartDate <= $value1[1] == false) {
                                continue;
                            }
                        }
                        if ($monthOfEndDate == $curr_mon) {
                            if ($dayOfEndDate >= $value1[0] && $dayOfEndDate <= $value1[1]) {
                                $break = true;
                            }
                        }
                        array_push($labels, $dt->format('Y M ') . $weeksLabel[$key1]);
                        if ($hub_type == '') {
                            $collectedWaste = DB::select("SELECT IFNULL(round(sum(cd.weight),2),0) as total from collection c left join collection_detail cd on c.id = cd.collection_id left join collection_hub chb on chb.id = c.collection_hub_id where month(c.created_at) = $curr_mon and c.status = 1 and year(c.created_at) = $curr_year and day(c.created_at) >= $value1[0] and day(c.created_at) <= $value1[1] and status = 1");

                            $soldWaste = DB::select("SELECT IFNULL(round(sum(wci.weight),2),0) as total from waste_clearance_schedule wcs left join waste_clearance_item wci on wcs.id = wci.waste_clearance_schedule_id left join collection_hub chb on chb.id = wcs.collection_hub_id where month(wcs.collection_time) = $curr_mon and year(wcs.collection_time) = $curr_year and day(wcs.collection_time) >= $value1[0] and day(wcs.collection_time) <= $value1[1] and wcs.status = 2");
                        } else {
                            $collectedWaste = DB::select("SELECT IFNULL(round(sum(cd.weight),2),0) as total from collection c left join collection_detail cd on c.id = cd.collection_id left join collection_hub chb on chb.id = c.collection_hub_id where month(c.created_at) = $curr_mon and c.status = 1 and year(c.created_at) = $curr_year and day(c.created_at) >= $value1[0] and day(c.created_at) <= $value1[1] and chb.type = $hub_type and status = 1");

                            $soldWaste = DB::select("SELECT IFNULL(round(sum(wci.weight),2),0) as total from waste_clearance_schedule wcs left join waste_clearance_item wci on wcs.id = wci.waste_clearance_schedule_id left join collection_hub chb on chb.id = wcs.collection_hub_id where month(wcs.collection_time) = $curr_mon and year(wcs.collection_time) = $curr_year and day(wcs.collection_time) >= $value1[0] and day(wcs.collection_time) <= $value1[1] and chb.type = $hub_type and wcs.status = 2");
                        }

                        $collectedWaste = array_column($collectedWaste, 'total')[0];
                        $soldWaste = array_column($soldWaste, 'total')[0];
                        array_push($collectedWasteTotal, $collectedWaste);
                        array_push($soldWasteTotal, $soldWaste);
                    }
                }
            }
        }
        $categoryColor = Helper::randomColor(2);
        return array($labels, $collectedWasteTotal, $soldWasteTotal, $categoryColor);
    }

    public function get_collection_waste_selling_district(Request $request)
    {
        $hub_type = $request->hub_type;
        $state_id = $request->state_id;
        $format = $request->format;
        $curr_mon = date('m');
        $curr_year = date('Y');

        $months = Helper::monthInThisYear();
        $weeks = Helper::dayInWeek();
        $weeksLabel = Helper::weekLabel();

        $category = RecycleCategory::orderBy('id')->get();
        $collectedWaste = array();
        $soldWaste = array();

        $date_from = $request->date_from;
        $date_to = $request->date_to;
        $labels = array();

        if ($date_from == '' && $date_to == '') {

            if ($format == 'Month') {
                foreach ($category as $value) {
                    $collectedWasteByMonth = array();
                    $soldWasteByMonth = array();
                    foreach ($months[0] as $value1) {
                        $soldWasteEvery = DB::select("SELECT ifnull(round(sum(B.weight),2),0) as total, ch.hub_name as name FROM (SELECT wcs.id,wci.weight,wcs.collection_hub_id from waste_clearance_schedule wcs left join waste_clearance_item wci on wcs.id = wci.waste_clearance_schedule_id left join recycle_type rt on wci.recycle_type_id = rt.id left join collection_hub chb on chb.id = wcs.collection_hub_id where month(wcs.collection_time) = $value1 and year(wcs.collection_time) = $curr_year and rt.recycle_category_id = $value->id and chb.hub_state_id = $state_id and chb.type = $hub_type and wcs.status = 2) AS B RIGHT JOIN collection_hub ch on ch.id = B.collection_hub_id where ch.hub_state_id = $state_id and ch.type = $hub_type group by ch.id,ch.hub_name order by ch.id");

                        $collectedWasteEvery = DB::select("SELECT ifnull(round(sum(B.weight),2),0) as total, ch.hub_name as name FROM (SELECT c.id,cd.weight,c.collection_hub_id from collection c left join collection_detail cd on c.id = cd.collection_id left join collection_hub chb on chb.id = c.collection_hub_id left join recycle_type rt on cd.recycling_type_id = rt.id where month(c.created_at) = $value1 and c.status = 1 and year(c.created_at) = $curr_year and rt.recycle_category_id = $value->id and chb.hub_state_id = $state_id and chb.type = $hub_type and c.status = 1) AS B RIGHT JOIN collection_hub ch on ch.id = B.collection_hub_id where ch.hub_state_id = $state_id and ch.type = $hub_type group by ch.id,ch.hub_name order by ch.id");

                        $soldWasteEvery = array_column($soldWasteEvery, 'total');
                        $collectedWasteEvery = array_column($collectedWasteEvery, 'total');

                        array_push($collectedWasteByMonth, $collectedWasteEvery);
                        array_push($soldWasteByMonth, $soldWasteEvery);
                    }
                    array_push($collectedWaste, $collectedWasteByMonth);
                    array_push($soldWaste, $soldWasteByMonth);
                }
                $labels = $months[1];
            }
            if ($format == 'Week') {
                foreach ($category as $value) {
                    $collectedWasteByWeek = array();
                    $soldWasteByWeek = array();
                    foreach ($weeks as $value1) {
                        $soldWasteEvery = DB::select("SELECT ifnull(round(sum(B.weight),2),0) as total, ch.hub_name as name FROM (SELECT wcs.id,wci.weight,wcs.collection_hub_id from waste_clearance_schedule wcs left join waste_clearance_item wci on wcs.id = wci.waste_clearance_schedule_id left join recycle_type rt on wci.recycle_type_id = rt.id left join collection_hub chb on chb.id = wcs.collection_hub_id where month(wcs.collection_time) = $curr_mon and year(wcs.collection_time) = $curr_year and day(wcs.collection_time) >= $value1[0] and day(wcs.collection_time) <= $value1[1] and rt.recycle_category_id = $value->id and chb.hub_state_id = $state_id and chb.type = $hub_type and wcs.status = 2) AS B RIGHT JOIN collection_hub ch on ch.id = B.collection_hub_id where ch.hub_state_id = $state_id and ch.type = $hub_type group by ch.id,ch.hub_name order by ch.id");

                        $collectedWasteEvery = DB::select("SELECT ifnull(round(sum(B.weight),2),0) as total, ch.hub_name as name FROM (SELECT c.id,cd.weight,c.collection_hub_id from collection c left join collection_detail cd on c.id = cd.collection_id left join collection_hub chb on chb.id = c.collection_hub_id left join recycle_type rt on cd.recycling_type_id = rt.id where month(c.created_at) = $curr_mon and c.status = 1 and year(c.created_at) = $curr_year and day(c.created_at) >= $value1[0] and day(c.created_at) <= $value1[1] and rt.recycle_category_id = $value->id and chb.hub_state_id = $state_id and chb.type = $hub_type and c.status = 1) AS B RIGHT JOIN collection_hub ch on ch.id = B.collection_hub_id where ch.hub_state_id = $state_id and ch.type = $hub_type group by ch.id,ch.hub_name order by ch.id");

                        $soldWasteEvery = array_column($soldWasteEvery, 'total');
                        $collectedWasteEvery = array_column($collectedWasteEvery, 'total');

                        array_push($collectedWasteByWeek, $collectedWasteEvery);
                        array_push($soldWasteByWeek, $soldWasteEvery);
                    }
                    array_push($collectedWaste, $collectedWasteByWeek);
                    array_push($soldWaste, $soldWasteByWeek);
                }
                $labels = $weeksLabel;
            }
        } else {
            $start    = (new DateTime($date_from))->modify('first day of this month');
            $end      = (new DateTime($date_to))->modify('first day of next month');
            $interval = DateInterval::createFromDateString('1 month');
            $period   = new DatePeriod($start, $interval, $end);
            $dayOfStartDate = date('d', strtotime($date_from));
            $dayOfEndDate = date('d', strtotime($date_to));
            $monthOfEndDate = date('m', strtotime($date_to));

            if ($format == 'Month') {
                foreach ($category as $index => $value) {
                    $collectedWasteByMonth = array();
                    $soldWasteByMonth = array();
                    foreach ($period as $key => $dt) {
                        $year = $dt->format('Y');
                        $month = $dt->format('m');

                        if ($index == 0)
                            array_push($labels, $dt->format('M Y'));

                        $soldWasteEvery = DB::select("SELECT ifnull(round(sum(B.weight),2),0) as total, ch.hub_name as name FROM (SELECT wcs.id,wci.weight,wcs.collection_hub_id from waste_clearance_schedule wcs left join waste_clearance_item wci on wcs.id = wci.waste_clearance_schedule_id left join recycle_type rt on wci.recycle_type_id = rt.id left join collection_hub chb on chb.id = wcs.collection_hub_id where wcs.collection_time >= '$date_from' and wcs.collection_time <= '$date_to' and month(wcs.collection_time) = $month and year(wcs.collection_time) = $year and rt.recycle_category_id = $value->id and chb.hub_state_id = $state_id and chb.type = $hub_type and wcs.status = 2) AS B RIGHT JOIN collection_hub ch on ch.id = B.collection_hub_id where ch.hub_state_id = $state_id and ch.type = $hub_type group by ch.id,ch.hub_name order by ch.id");

                        $collectedWasteEvery = DB::select("SELECT ifnull(round(sum(B.weight),2),0) as total, ch.hub_name as name FROM (SELECT c.id,cd.weight,c.collection_hub_id from collection c left join collection_detail cd on c.id = cd.collection_id left join collection_hub chb on chb.id = c.collection_hub_id left join recycle_type rt on cd.recycling_type_id = rt.id where c.created_at >= '$date_from' and c.status = 1 and c.created_at <= '$date_to' and month(c.created_at) = $month and year(c.created_at) = $year and rt.recycle_category_id = $value->id and chb.hub_state_id = $state_id and chb.type = $hub_type and c.status = 1) AS B RIGHT JOIN collection_hub ch on ch.id = B.collection_hub_id where ch.hub_state_id = $state_id and ch.type = $hub_type group by ch.id,ch.hub_name order by ch.id");

                        $soldWasteEvery = array_column($soldWasteEvery, 'total');
                        $collectedWasteEvery = array_column($collectedWasteEvery, 'total');

                        array_push($collectedWasteByMonth, $collectedWasteEvery);
                        array_push($soldWasteByMonth, $soldWasteEvery);
                    }
                    array_push($collectedWaste, $collectedWasteByMonth);
                    array_push($soldWaste, $soldWasteByMonth);
                }
            }
            if ($format == 'Week') {
                foreach ($category as $index => $value) {
                    $collectedWasteByWeek = array();
                    $soldWasteByWeek = array();
                    foreach ($period as $key => $dt) {
                        $break = false;
                        $curr_year = $dt->format('Y');
                        $curr_mon = $dt->format('m');
                        foreach ($weeks as $key1 => $value1) {
                            if ($break == true)
                                break;
                            if ($key == 0) {
                                if ($dayOfStartDate >= $value1[0] && $dayOfStartDate <= $value1[1] == false) {
                                    continue;
                                }
                            }
                            if ($monthOfEndDate == $curr_mon) {
                                if ($dayOfEndDate >= $value1[0] && $dayOfEndDate <= $value1[1]) {
                                    $break = true;
                                }
                            }
                            if ($index == 0)
                                array_push($labels, $dt->format('Y M ') . $weeksLabel[$key1]);

                            $soldWasteEvery = DB::select("SELECT ifnull(round(sum(B.weight),2),0) as total, ch.hub_name as name FROM (SELECT wcs.id,wci.weight,wcs.collection_hub_id from waste_clearance_schedule wcs left join waste_clearance_item wci on wcs.id = wci.waste_clearance_schedule_id left join recycle_type rt on wci.recycle_type_id = rt.id left join collection_hub chb on chb.id = wcs.collection_hub_id where month(wcs.collection_time) = $curr_mon and year(wcs.collection_time) = $curr_year and day(wcs.collection_time) >= $value1[0] and day(wcs.collection_time) <= $value1[1] and rt.recycle_category_id = $value->id and chb.hub_state_id = $state_id and chb.type = $hub_type and wcs.status = 2) AS B RIGHT JOIN collection_hub ch on ch.id = B.collection_hub_id where ch.hub_state_id = $state_id and ch.type = $hub_type group by ch.id,ch.hub_name order by ch.id");

                            $collectedWasteEvery = DB::select("SELECT ifnull(round(sum(B.weight),2),0) as total, ch.hub_name as name FROM (SELECT c.id,cd.weight,c.collection_hub_id from collection c left join collection_detail cd on c.id = cd.collection_id left join collection_hub chb on chb.id = c.collection_hub_id left join recycle_type rt on cd.recycling_type_id = rt.id where month(c.created_at) = $curr_mon and c.status = 1 and year(c.created_at) = $curr_year and day(c.created_at) >= $value1[0] and day(c.created_at) <= $value1[1] and rt.recycle_category_id = $value->id and chb.hub_state_id = $state_id and chb.type = $hub_type and c.status = 1) AS B RIGHT JOIN collection_hub ch on ch.id = B.collection_hub_id where ch.hub_state_id = $state_id and ch.type = $hub_type group by ch.id,ch.hub_name order by ch.id");

                            $soldWasteEvery = array_column($soldWasteEvery, 'total');
                            $collectedWasteEvery = array_column($collectedWasteEvery, 'total');

                            array_push($collectedWasteByWeek, $collectedWasteEvery);
                            array_push($soldWasteByWeek, $soldWasteEvery);
                        }
                    }
                    array_push($collectedWaste, $collectedWasteByWeek);
                    array_push($soldWaste, $soldWasteByWeek);
                }
            }
        }
        $hubs = CollectionHub::where('hub_state_id', $state_id)->where('type', $hub_type)->get();
        $color = Helper::randomColor(($hubs->count()) * 2);


        return array($color, $hubs, $labels, $collectedWaste, $soldWaste, $category->toArray());
    }

    public function demography()
    {
        $allStates = State::get()->toArray();
        return view('report.app_performance.demography', compact('allStates'));
    }

    public function get_registered_user(Request $request)
    {
        $curr_mon = date('m');
        $curr_year = date('Y');
        $format = $request->format;
        $months = Helper::monthInThisYear();
        $weeks = Helper::dayInWeek();
        $weeksLabel = Helper::weekLabel();
        $allStates = State::get()->toArray();
        $allStatesName = array();
        $date_from = $request->date_from;
        $date_to = $request->date_to;
        $individual = array();
        $company = array();
        $total = array();
        $labels = array();

        if ($date_from == '' && $date_to == '') {
            if ($format == 'Month') {
                foreach ($allStates as $index1 => $value1) {
                    $state_id = $value1['id'];
                    $stateData = array();
                    foreach ($months[0] as  $index => $value) {
                        $number = cal_days_in_month(CAL_GREGORIAN, $value, $curr_year);
                        $date = $curr_year . '-' . $value . '-' . $number;
                        if ($index1 == 0) {
                            $individualMonth = DB::select("SELECT count(c.id) as total from customer c where c.isIndividual = 1 and c.created_at <= '$date'");

                            $companyMonth = DB::select("SELECT count(c.id) as total from customer c where c.isIndividual = 0 and c.created_at <= '$date'");

                            $totalMonth = DB::select("SELECT count(c.id) as total from customer c where c.created_at <= '$date'");

                            array_push($individual, array_column($individualMonth, 'total')[0]);
                            array_push($company, array_column($companyMonth, 'total')[0]);
                            array_push($total, array_column($totalMonth, 'total')[0]);
                        }

                        $totalMonth = DB::select("SELECT count(c.id) as total from customer c where c.state = $state_id and c.created_at <= '$date'");
                        $stateData[] = array_column($totalMonth, 'total')[0];
                    }
                    $dataAllStates[] = $stateData;
                    $allStatesName[] = $value1['name'];
                }
                $labels = $months[1];
            }
            if ($format == 'Week') {
                foreach ($allStates as $index1 => $value1) {
                    $state_id = $value1['id'];
                    $stateData = array();
                    foreach ($weeks as $indexWeek => $value) {
                        if(date('d') < $value[1]){
                            break;
                        }
                        $date = $curr_year . '-' . $curr_mon . '-' . $value[1];
                        if ($index1 == 0) {
                            $labels[] = $weeksLabel[$indexWeek];
                            $individualWeek = DB::select("SELECT count(c.id) as total from customer c where c.isIndividual = 1 and c.created_at <= '$date'");
                            $companyWeek = DB::select("SELECT count(c.id) as total from customer c where c.isIndividual = 0 and c.created_at <= '$date'");

                            $totalWeek = DB::select("SELECT count(c.id) as total from customer c where c.created_at <= '$date'");

                            array_push($individual, array_column($individualWeek, 'total')[0]);
                            array_push($company, array_column($companyWeek, 'total')[0]);
                            array_push($total, array_column($totalWeek, 'total')[0]);
                        }
                        $totalWeek = DB::select("SELECT count(c.id) as total from customer c where c.state = $state_id and year(c.created_at) = $curr_year and c.created_at <= '$date'");
                        $stateData[] = array_column($totalWeek, 'total')[0];
                    }
                    $dataAllStates[] = $stateData;
                    $allStatesName[] = $value1['name'];
                }
            }
        } else {
            $start    = (new DateTime($date_from))->modify('first day of this month');
            $end      = (new DateTime($date_to))->modify('first day of next month');
            $interval = DateInterval::createFromDateString('1 month');
            $period   = new DatePeriod($start, $interval, $end);
            $dayOfStartDate = date('d', strtotime($date_from));
            $dayOfEndDate = date('d', strtotime($date_to));
            $monthOfEndDate = date('m', strtotime($date_to));

            if ($format == 'Month') {
                foreach ($allStates as $index1 => $value1) {
                    $state_id = $value1['id'];
                    $stateData = array();
                    foreach ($period as $key => $dt) {
                        $year = $dt->format('Y');
                        $month = $dt->format('m');
                        $number = cal_days_in_month(CAL_GREGORIAN, $month, $year);
                        $date = $year . '-' . $month . '-' . $number;

                        if ($index1 == 0) {
                            array_push($labels, $dt->format('M Y'));

                            $individualMonth = DB::select("SELECT count(c.id) as total from customer c where c.isIndividual = 1  and c.created_at <= '$date'");

                            $companyMonth = DB::select("SELECT count(c.id) as total from customer c where c.isIndividual = 0 and c.created_at <= '$date'");

                            $totalMonth = DB::select("SELECT count(c.id) as total from customer c where c.created_at <= '$date'");
                            array_push($individual, array_column($individualMonth, 'total')[0]);
                            array_push($company, array_column($companyMonth, 'total')[0]);
                            array_push($total, array_column($totalMonth, 'total')[0]);
                        }

                        $totalMonth = DB::select("SELECT count(c.id) as total from customer c where c.state = $state_id and c.created_at <= '$date'");
                        $stateData[] = array_column($totalMonth, 'total')[0];
                    }
                    $dataAllStates[] = $stateData;
                    $allStatesName[] = $value1['name'];
                }
            }
            if ($format == 'Week') {
                foreach ($allStates as $index1 => $value1) {
                    $state_id = $value1['id'];
                    $stateData = array();
                    foreach ($period as $key => $dt) {
                        $break = false;
                        $curr_year = $dt->format('Y');
                        $curr_mon = $dt->format('m');
                        foreach ($weeks as $key1 => $value) {
                            if ($break == true)
                                break;
                            if ($key == 0) {
                                if ($dayOfStartDate >= $value[0] && $dayOfStartDate <= $value[1] == false) {
                                    continue;
                                }
                            }
                            if ($monthOfEndDate == $curr_mon) {
                                if ($dayOfEndDate >= $value[0] && $dayOfEndDate <= $value[1]) {
                                    $break = true;
                                }
                            }

                            $date = $curr_year . '-' . $curr_mon . '-' . $value[1];
                            if ($index1 == 0) {
                                array_push($labels, $dt->format('Y M ') . $weeksLabel[$key1]);

                                $individualWeek = DB::select("SELECT count(c.id) as total from customer c where c.isIndividual = 1 and c.created_at <= '$date'");
                                $companyWeek = DB::select("SELECT count(c.id) as total from customer c where c.isIndividual = 0 and c.created_at <= '$date'");

                                $totalWeek = DB::select("SELECT count(c.id) as total from customer c where c.created_at <= '$date'");

                                array_push($individual, array_column($individualWeek, 'total')[0]);
                                array_push($company, array_column($companyWeek, 'total')[0]);
                                array_push($total, array_column($totalWeek, 'total')[0]);
                            }
                            $totalWeek = DB::select("SELECT count(c.id) as total from customer c where c.state = $state_id and c.created_at <= '$date'");
                            $stateData[] = array_column($totalWeek, 'total')[0];
                        }
                    }
                    $dataAllStates[] = $stateData;
                    $allStatesName[] = $value1['name'];
                }
            }
        }

        $color = Helper::randomColor($allStatesName);
        return array($labels, $individual, $company, $total, $dataAllStates, $allStatesName, $color);
    }

    public function get_registered_user_district(Request $request)
    {
        $hub_type = $request->hub_type;
        $format = $request->format;
        $state_id = $request->state_id;
        $months = Helper::monthInThisYear();
        $weeks = Helper::dayInWeek();
        $weeksLabel = Helper::weekLabel();
        $curr_mon = date('m');
        $curr_year = date('Y');
        $allCities = City::where('state_id', $state_id)->get()->toArray();
        $date_from = $request->date_from;
        $date_to = $request->date_to;
        $labels = array();
        if ($date_from == '' && $date_to == '') {
            foreach ($allCities as $index => $value) {
                $dataMonth = array();
                if ($format == 'Month') {
                    foreach ($months[0] as $value1) {
                        $number = cal_days_in_month(CAL_GREGORIAN, $value1, $curr_year);
                        $date = $curr_year . '-' . $value1 . '-' . $number;
                        
                        $city_id = $value['id'];
                        $dataCities = DB::select("SELECT count(c.id) as total from customer c where c.city = $city_id and c.created_at <= '$date'");

                        if ($index == 0) {
                            $dataStates = DB::select("SELECT count(c.id) as total from customer c where c.state = $state_id and c.created_at <= '$date'");
                            $dataAll[] = array_column($dataStates, 'total')[0];
                        }
                        $dataMonth[] = array_column($dataCities, 'total')[0];
                    }
                    $cityData[] = $dataMonth;
                    $city[] = $value['name'];
                    $labels = $months[1];
                }
                if ($format == 'Week') {
                    foreach ($weeks as $indexWeek => $value1) {
                        if(date('d') < $value1[1]){
                            break;
                        }
                        
                        $city_id = $value['id'];
                        $date = $curr_year . '-' . $curr_mon . '-' . $value1[1];
                        $dataCities = DB::select("SELECT count(c.id) as total from customer c where c.city = $city_id and c.created_at <= '$date'");

                        if ($index == 0) {
                            $labels[] = $weeksLabel[$indexWeek];
                            $dataStates = DB::select("SELECT count(c.id) as total from customer c where c.state = $state_id and c.created_at <= '$date'  ");
                            $dataAll[] = array_column($dataStates, 'total')[0];
                        }
                        $dataMonth[] = array_column($dataCities, 'total')[0];
                    }
                    $cityData[] = $dataMonth;
                    $city[] = $value['name'];
                }
            }
        } else {
            $start    = (new DateTime($date_from))->modify('first day of this month');
            $end      = (new DateTime($date_to))->modify('first day of next month');
            $interval = DateInterval::createFromDateString('1 month');
            $period   = new DatePeriod($start, $interval, $end);
            $dayOfStartDate = date('d', strtotime($date_from));
            $dayOfEndDate = date('d', strtotime($date_to));
            $monthOfEndDate = date('m', strtotime($date_to));

            foreach ($allCities as $index => $value) {
                $dataMonth = array();
                if ($format == 'Month') {
                    foreach ($period as $key => $dt) {
                        $year = $dt->format('Y');
                        $month = $dt->format('m');

                        $number = cal_days_in_month(CAL_GREGORIAN, $month, $year);
                        $date = $year . '-' . $month . '-' . $number;

                        $city_id = $value['id'];
                        $dataCities = DB::select("SELECT count(c.id) as total from customer c where c.city = $city_id and c.created_at <= '$date'");

                        if ($index == 0) {
                            array_push($labels, $dt->format('M Y'));

                            $dataStates = DB::select("SELECT count(c.id) as total from customer c where c.state = $state_id and c.created_at <= '$date'");
                            $dataAll[] = array_column($dataStates, 'total')[0];
                        }
                        $dataMonth[] = array_column($dataCities, 'total')[0];
                    }
                    $cityData[] = $dataMonth;
                    $city[] = $value['name'];
                }
                if ($format == 'Week') {
                    foreach ($period as $key => $dt) {
                        $break = false;
                        $curr_year = $dt->format('Y');
                        $curr_mon = $dt->format('m');
                        foreach ($weeks as $key1 => $value1) {
                            if ($break == true)
                                break;
                            if ($key == 0) {
                                if ($dayOfStartDate >= $value1[0] && $dayOfStartDate <= $value1[1] == false) {
                                    continue;
                                }
                            }
                            if ($monthOfEndDate == $curr_mon) {
                                if ($dayOfEndDate >= $value1[0] && $dayOfEndDate <= $value1[1]) {
                                    $break = true;
                                }
                            }
                            $city_id = $value['id'];
                            $date = $curr_year . '-' . $curr_mon . '-' . $value1[1];
                            $dataCities = DB::select("SELECT count(c.id) as total from customer c where c.city = $city_id and c.created_at <= '$date'");

                            if ($index == 0) {
                                array_push($labels, $dt->format('Y M ') . $weeksLabel[$key1]);
                                $dataStates = DB::select("SELECT count(c.id) as total from customer c where c.state = $state_id and c.created_at <= '$date'");
                                $dataAll[] = array_column($dataStates, 'total')[0];
                            }
                            $dataMonth[] = array_column($dataCities, 'total')[0];
                        }
                    }
                    $cityData[] = $dataMonth;
                    $city[] = $value['name'];
                }
            }
        }

        $colors = Helper::randomColor($allCities);
        $array = array($city, $colors, $cityData, $labels, $dataAll);
        return $array;
    }

    public function get_new_registered_user(Request $request)
    {
        $curr_mon = date('m');
        $curr_year = date('Y');
        $format = $request->format;
        $months = Helper::monthInThisYear();
        $weeks = Helper::dayInWeek();
        $weeksLabel = Helper::weekLabel();
        $allStates = State::get()->toArray();
        $allStatesName = array();

        $individual = array();
        $company = array();
        $total = array();
        $date_from = $request->date_from;
        $date_to = $request->date_to;
        $labels = array();

        if ($date_from == '' && $date_to == '') {
            $tableData = DB::select("SELECT concat(year(created_at),'-',month(c.created_at)) as date, c.email, IF(c.isIndividual=1, 'B2C', 'B2B') as type,s.name as state, ct.name as city FROM customer c left join state s on c.state = s.id left join city ct on c.city = ct.id");
            if ($format == 'Month') {
                foreach ($allStates as $index1 => $value1) {
                    $state_id = $value1['id'];
                    $stateData = array();
                    foreach ($months[0] as  $index => $value) {
                        if ($index1 == 0) {
                            $individualMonth = DB::select("SELECT count(c.id) as total from customer c where c.isIndividual = 1 and month(c.created_at) = $value and year(c.created_at) = $curr_year");

                            $companyMonth = DB::select("SELECT count(c.id) as total from customer c where c.isIndividual = 0 and month(c.created_at) = $value and year(c.created_at) = $curr_year");

                            $totalMonth = DB::select("SELECT count(c.id) as total from customer c where month(c.created_at) = $value and year(c.created_at) = $curr_year");
                            array_push($individual, array_column($individualMonth, 'total')[0]);
                            array_push($company, array_column($companyMonth, 'total')[0]);
                            array_push($total, array_column($totalMonth, 'total')[0]);
                        }

                        $totalMonth = DB::select("SELECT count(c.id) as total from customer c where c.state = $state_id and month(c.created_at) = $value and year(c.created_at) = $curr_year");
                        $stateData[] = array_column($totalMonth, 'total')[0];
                    }
                    $dataAllStates[] = $stateData;
                    $allStatesName[] = $value1['name'];
                }
                $labels = $months[1];
            }
            if ($format == 'Week') {
                foreach ($allStates as $index1 => $value1) {
                    $state_id = $value1['id'];
                    $stateData = array();
                    foreach ($weeks as $value) {
                        if ($index1 == 0) {
                            $individualWeek = DB::select("SELECT count(c.id) as total from customer c where c.isIndividual = 1 and  year(c.created_at) = $curr_year and month(c.created_at) = $curr_mon and day(c.created_at) >= $value[0] and day(c.created_at) <= $value[1] ");
                            $companyWeek = DB::select("SELECT count(c.id) as total from customer c where c.isIndividual = 0 and  year(c.created_at) = $curr_year and month(c.created_at) = $curr_mon and day(c.created_at) >= $value[0] and day(c.created_at) <= $value[1]");

                            $totalWeek = DB::select("SELECT count(c.id) as total from customer c where  month(c.created_at) = $curr_mon and day(c.created_at) >= $value[0] and day(c.created_at) <= $value[1] and year(c.created_at) = $curr_year ");

                            array_push($individual, array_column($individualWeek, 'total')[0]);
                            array_push($company, array_column($companyWeek, 'total')[0]);
                            array_push($total, array_column($totalWeek, 'total')[0]);
                        }
                        $totalWeek = DB::select("SELECT count(c.id) as total from customer c where c.state = $state_id and year(c.created_at) = $curr_year and month(c.created_at) = $curr_mon and day(c.created_at) >= $value[0] and day(c.created_at) <= $value[1]");
                        $stateData[] = array_column($totalWeek, 'total')[0];
                    }
                    $dataAllStates[] = $stateData;
                    $allStatesName[] = $value1['name'];
                }
                $labels = $weeksLabel;
            }
        } else {
            $start    = (new DateTime($date_from))->modify('first day of this month');
            $end      = (new DateTime($date_to))->modify('first day of next month');
            $interval = DateInterval::createFromDateString('1 month');
            $period   = new DatePeriod($start, $interval, $end);
            $dayOfStartDate = date('d', strtotime($date_from));
            $dayOfEndDate = date('d', strtotime($date_to));
            $monthOfEndDate = date('m', strtotime($date_to));
            $tableData = DB::select("SELECT concat(year(created_at),'-',month(c.created_at)) as date, c.email, IF(c.isIndividual=1, 'B2C', 'B2B') as type,s.name as state, ct.name as city FROM customer c left join state s on c.state = s.id left join city ct on ct.id = c.city where c.created_at >= '$date_from' and c.created_at <= '$date_to'");
            if ($format == 'Month') {
                foreach ($allStates as $index1 => $value1) {
                    $state_id = $value1['id'];
                    $stateData = array();
                    foreach ($period as $key => $dt) {
                        $year = $dt->format('Y');
                        $month = $dt->format('m');

                        if ($index1 == 0) {
                            array_push($labels, $dt->format('M Y'));
                            $individualMonth = DB::select("SELECT count(c.id) as total from customer c where c.isIndividual = 1 and c.created_at >= '$date_from' and c.created_at <= '$date_to' and month(c.created_at) = $month and year(c.created_at) = $year");

                            $companyMonth = DB::select("SELECT count(c.id) as total from customer c where c.isIndividual = 0 and c.created_at >= '$date_from' and c.created_at <= '$date_to' and month(c.created_at) = $month and year(c.created_at) = $year");

                            $totalMonth = DB::select("SELECT count(c.id) as total from customer c where month(c.created_at) = $month and c.created_at >= '$date_from' and c.created_at <= '$date_to' and year(c.created_at) = $year");
                            array_push($individual, array_column($individualMonth, 'total')[0]);
                            array_push($company, array_column($companyMonth, 'total')[0]);
                            array_push($total, array_column($totalMonth, 'total')[0]);
                        }

                        $totalMonth = DB::select("SELECT count(c.id) as total from customer c where c.state = $state_id and c.created_at >= '$date_from' and c.created_at <= '$date_to' and month(c.created_at) = $month and year(c.created_at) = $year");
                        $stateData[] = array_column($totalMonth, 'total')[0];
                    }
                    $dataAllStates[] = $stateData;
                    $allStatesName[] = $value1['name'];
                }
            }
            if ($format == 'Week') {
                foreach ($allStates as $index1 => $value1) {
                    $state_id = $value1['id'];
                    $stateData = array();
                    foreach ($period as $key => $dt) {
                        $break = false;
                        $curr_year = $dt->format('Y');
                        $curr_mon = $dt->format('m');
                        foreach ($weeks as $key1 => $value) {
                            if ($break == true)
                                break;
                            if ($key == 0) {
                                if ($dayOfStartDate >= $value[0] && $dayOfStartDate <= $value[1] == false) {
                                    continue;
                                }
                            }
                            if ($monthOfEndDate == $curr_mon) {
                                if ($dayOfEndDate >= $value[0] && $dayOfEndDate <= $value[1]) {
                                    $break = true;
                                }
                            }
                            if ($index1 == 0) {
                                array_push($labels, $dt->format('Y M ') . $weeksLabel[$key1]);

                                $individualWeek = DB::select("SELECT count(c.id) as total from customer c where c.isIndividual = 1 and  year(c.created_at) = $curr_year and month(c.created_at) = $curr_mon and day(c.created_at) >= $value[0] and day(c.created_at) <= $value[1] ");
                                $companyWeek = DB::select("SELECT count(c.id) as total from customer c where c.isIndividual = 0 and  year(c.created_at) = $curr_year and month(c.created_at) = $curr_mon and day(c.created_at) >= $value[0] and day(c.created_at) <= $value[1]");

                                $totalWeek = DB::select("SELECT count(c.id) as total from customer c where  month(c.created_at) = $curr_mon and day(c.created_at) >= $value[0] and day(c.created_at) <= $value[1] and year(c.created_at) = $curr_year ");

                                array_push($individual, array_column($individualWeek, 'total')[0]);
                                array_push($company, array_column($companyWeek, 'total')[0]);
                                array_push($total, array_column($totalWeek, 'total')[0]);
                            }
                            $totalWeek = DB::select("SELECT count(c.id) as total from customer c where c.state = $state_id and year(c.created_at) = $curr_year and month(c.created_at) = $curr_mon and day(c.created_at) >= $value[0] and day(c.created_at) <= $value[1]");
                            $stateData[] = array_column($totalWeek, 'total')[0];
                        }
                    }
                    $dataAllStates[] = $stateData;
                    $allStatesName[] = $value1['name'];
                }
            }
        }


        $color = Helper::randomColor($allStatesName);
        return array($labels, $individual, $company, $total, $dataAllStates, $allStatesName, $color, $tableData);
    }

    public function get_new_registered_user_district(Request $request)
    {
        $date_from = $request->date_from;
        $date_to = $request->date_to;
        $format = $request->format;
        $state_id = $request->state_id;
        $months = Helper::monthInThisYear();
        $weeks = Helper::dayInWeek();
        $weeksLabel = Helper::weekLabel();
        $curr_mon = date('m');
        $curr_year = date('Y');
        $allCities = City::where('state_id', $state_id)->get()->toArray();
        $labels = array();
        if ($date_from == '' && $date_to == '') {
            foreach ($allCities as $index => $value) {
                $dataMonth = array();
                if ($format == 'Month') {
                    if ($index == 0)
                        $labels = $months[1];
                    foreach ($months[0] as $value1) {
                        $city_id = $value['id'];
                        $dataCities = DB::select("SELECT count(c.id) as total from customer c where c.city = $city_id and month(c.created_at) = $value1 and year(c.created_at) = $curr_year  ");

                        if ($index == 0) {
                            $dataStates = DB::select("SELECT count(c.id) as total from customer c where c.state = $state_id and month(c.created_at) = $value1 and year(c.created_at) = $curr_year ");
                            $dataAll[] = array_column($dataStates, 'total')[0];
                        }
                        $dataMonth[] = array_column($dataCities, 'total')[0];
                    }
                    $cityData[] = $dataMonth;
                    $city[] = $value['name'];
                }
                if ($format == 'Week') {
                    if ($index == 0)
                        $labels = $weeksLabel;
                    foreach ($weeks as $value1) {
                        $city_id = $value['id'];
                        $dataCities = DB::select("SELECT count(c.id) as total from customer c where c.city = $city_id and year(c.created_at) = $curr_year and month(c.created_at) = $curr_mon and day(c.created_at) >= $value1[0] and day(c.created_at) <= $value1[1] ");

                        if ($index == 0) {
                            $dataStates = DB::select("SELECT count(c.id) as total from customer c where c.state = $state_id and year(c.created_at) = $curr_year and month(c.created_at) = $curr_mon and day(c.created_at) >= $value1[0] and day(c.created_at) <= $value1[1]");
                            $dataAll[] = array_column($dataStates, 'total')[0];
                        }
                        $dataMonth[] = array_column($dataCities, 'total')[0];
                    }
                    $cityData[] = $dataMonth;
                    $city[] = $value['name'];
                }
            }
        } else {
            $start    = (new DateTime($date_from))->modify('first day of this month');
            $end      = (new DateTime($date_to))->modify('first day of next month');
            $interval = DateInterval::createFromDateString('1 month');
            $period   = new DatePeriod($start, $interval, $end);
            $dayOfStartDate = date('d', strtotime($date_from));
            $dayOfEndDate = date('d', strtotime($date_to));
            $monthOfEndDate = date('m', strtotime($date_to));

            foreach ($allCities as $index => $value) {
                $dataMonth = array();
                if ($format == 'Month') {
                    foreach ($period as $key => $dt) {
                        $year = $dt->format('Y');
                        $month = $dt->format('m');

                        $city_id = $value['id'];
                        $dataCities = DB::select("SELECT count(c.id) as total from customer c where c.city = $city_id and c.created_at >= '$date_from' and c.created_at <= '$date_to' and month(c.created_at) = $month and year(c.created_at) = $year  ");

                        if ($index == 0) {
                            array_push($labels, $dt->format('M Y'));
                            $dataStates = DB::select("SELECT count(c.id) as total from customer c where c.state = $state_id and month(c.created_at) = $month and year(c.created_at) = $year and c.created_at >= '$date_from' and c.created_at <= '$date_to'");
                            $dataAll[] = array_column($dataStates, 'total')[0];
                        }
                        $dataMonth[] = array_column($dataCities, 'total')[0];
                    }
                    $cityData[] = $dataMonth;
                    $city[] = $value['name'];
                }
                if ($format == 'Week') {
                    foreach ($period as $key => $dt) {
                        $break = false;
                        $curr_year = $dt->format('Y');
                        $curr_mon = $dt->format('m');
                        foreach ($weeks as $key1 => $value1) {
                            if ($break == true)
                                break;
                            if ($key == 0) {
                                if ($dayOfStartDate >= $value1[0] && $dayOfStartDate <= $value1[1] == false) {
                                    continue;
                                }
                            }
                            if ($monthOfEndDate == $curr_mon) {
                                if ($dayOfEndDate >= $value1[0] && $dayOfEndDate <= $value1[1]) {
                                    $break = true;
                                }
                            }
                            $city_id = $value['id'];
                            $dataCities = DB::select("SELECT count(c.id) as total from customer c where c.city = $city_id and year(c.created_at) = $curr_year and month(c.created_at) = $curr_mon and day(c.created_at) >= $value1[0] and day(c.created_at) <= $value1[1] ");

                            if ($index == 0) {
                                array_push($labels, $dt->format('Y M ') . $weeksLabel[$key1]);
                                $dataStates = DB::select("SELECT count(c.id) as total from customer c where c.state = $state_id and year(c.created_at) = $curr_year and month(c.created_at) = $curr_mon and day(c.created_at) >= $value1[0] and day(c.created_at) <= $value1[1]");
                                $dataAll[] = array_column($dataStates, 'total')[0];
                            }
                            $dataMonth[] = array_column($dataCities, 'total')[0];
                        }
                    }
                    $cityData[] = $dataMonth;
                    $city[] = $value['name'];
                }
            }
        }
        $colors = Helper::randomColor($allCities);
        $array = array($city, $colors, $cityData, $labels, $dataAll);
        return $array;
    }

    public function get_thirty_days_login(Request $request)
    {
        $date_from = $request->date_from;
        $date_to = $request->date_to;
        $curr_mon = date('m');
        $curr_year = date('Y');
        $format = $request->format;
        $months = Helper::monthInThisYear();
        $weeks = Helper::dayInWeek();
        $weeksLabel = Helper::weekLabel();
        $allStates = State::get()->toArray();
        $allStatesName = array();
        $tableData = array();
        $individual = array();
        $company = array();
        $total = array();
        if ($date_from == '' && $date_to == '') {
            $tableData = DB::select("SELECT DISTINCT(u.email) from login_activity la left join users u on la.user_id = u.id left join user_role ur on ur.user_id = u.id where ur.role_id = 2");
            if ($format == 'Month') {
                foreach ($allStates as $index1 => $value1) {
                    $state_id = $value1['id'];
                    $stateData = array();
                    foreach ($months[0] as  $index => $value) {
                        if ($index1 == 0) {
                            $individualMonth = DB::select("SELECT count(DISTINCT(la.user_id)) as total from login_activity la left join customer c on la.user_id = c.user_id where c.isIndividual = 1 and month(la.created_at) = $value and year(la.created_at) = $curr_year");

                            $companyMonth = DB::select("SELECT count(DISTINCT(la.user_id)) as total from login_activity la left join customer c on la.user_id = c.user_id where c.isIndividual = 0 and month(la.created_at) = $value and year(la.created_at) = $curr_year");

                            $totalMonth = DB::select("SELECT count(DISTINCT(la.user_id)) as total from login_activity la left join customer c on la.user_id = c.user_id where month(la.created_at) = $value and year(la.created_at) = $curr_year");
                            array_push($individual, array_column($individualMonth, 'total')[0]);
                            array_push($company, array_column($companyMonth, 'total')[0]);
                            array_push($total, array_column($totalMonth, 'total')[0]);
                        }

                        $totalMonth = DB::select("SELECT count(DISTINCT(la.user_id)) as total from login_activity la left join customer c on la.user_id = c.user_id where c.state = $state_id and month(la.created_at) = $value and year(la.created_at) = $curr_year");
                        $stateData[] = array_column($totalMonth, 'total')[0];
                    }
                    $dataAllStates[] = $stateData;
                    $allStatesName[] = $value1['name'];
                }
                $label = $months[1];
            }
            if ($format == 'Week') {
                foreach ($allStates as $index1 => $value1) {
                    $state_id = $value1['id'];
                    $stateData = array();
                    foreach ($weeks as $value) {
                        if ($index1 == 0) {
                            $individualWeek = DB::select("SELECT count(DISTINCT(la.user_id)) as total from login_activity la left join customer c on la.user_id = c.user_id where c.isIndividual = 1 and month(la.created_at) = $curr_mon and year(la.created_at) = $curr_year and day(la.created_at) >= $value[0] and day(la.created_at) <= $value[1]");

                            $companyWeek = DB::select("SELECT count(DISTINCT(la.user_id)) as total from login_activity la left join customer c on la.user_id = c.user_id where c.isIndividual = 0 and month(la.created_at) = $curr_mon and year(la.created_at) = $curr_year and day(la.created_at) >= $value[0] and day(la.created_at) <= $value[1]");

                            $totalWeek = DB::select("SELECT count(DISTINCT(la.user_id)) as total from login_activity la left join customer c on la.user_id = c.user_id where month(la.created_at) = $curr_mon and year(la.created_at) = $curr_year and day(la.created_at) >= $value[0] and day(la.created_at) <= $value[1]");

                            array_push($individual, array_column($individualWeek, 'total')[0]);
                            array_push($company, array_column($companyWeek, 'total')[0]);
                            array_push($total, array_column($totalWeek, 'total')[0]);
                        }
                        $totalWeek = DB::select("SELECT count(DISTINCT(la.user_id)) as total from login_activity la left join customer c on la.user_id = c.user_id where  c.state = $state_id and month(la.created_at) = $curr_mon and year(la.created_at) = $curr_year and day(la.created_at) >= $value[0] and day(la.created_at) <= $value[1]");
                        $stateData[] = array_column($totalWeek, 'total')[0];
                    }
                    $dataAllStates[] = $stateData;
                    $allStatesName[] = $value1['name'];
                }
                $label = $weeksLabel;
            }
        } else {
            $start    = (new DateTime($date_from))->modify('first day of this month');
            $end      = (new DateTime($date_to))->modify('first day of next month');
            $interval = DateInterval::createFromDateString('1 month');
            $period   = new DatePeriod($start, $interval, $end);
            $dayOfStartDate = date('d', strtotime($date_from));
            $dayOfEndDate = date('d', strtotime($date_to));
            $monthOfEndDate = date('m', strtotime($date_to));
            $label = array();
            $tableData = DB::select("SELECT DISTINCT(u.email) from login_activity la left join users u on la.user_id = u.id left join user_role ur on ur.user_id = u.id where ur.role_id = 2 and la.created_at >= '$date_from' and la.created_at <= '$date_to'");
            if ($format == 'Month') {
                foreach ($allStates as $index1 => $value1) {
                    $state_id = $value1['id'];
                    $stateData = array();
                    foreach ($period as $key => $dt) {
                        $year = $dt->format('Y');
                        $month = $dt->format('m');
                        if ($index1 == 0) {
                            array_push($label, $dt->format('M Y'));

                            $individualMonth = DB::select("SELECT count(DISTINCT(la.user_id)) as total from login_activity la left join customer c on la.user_id = c.user_id where c.isIndividual = 1 and la.created_at >= '$date_from' and la.created_at <= '$date_to' and month(la.created_at) = $month and year(la.created_at) = $year");

                            $companyMonth = DB::select("SELECT count(DISTINCT(la.user_id)) as total from login_activity la left join customer c on la.user_id = c.user_id where c.isIndividual = 0 and la.created_at >= '$date_from' and la.created_at <= '$date_to' and month(la.created_at) = $month and year(la.created_at) = $year");

                            $totalMonth = DB::select("SELECT count(DISTINCT(la.user_id)) as total from login_activity la left join customer c on la.user_id = c.user_id where month(la.created_at) = $month and year(la.created_at) = $year and la.created_at >= '$date_from' and la.created_at <= '$date_to'");
                            array_push($individual, array_column($individualMonth, 'total')[0]);
                            array_push($company, array_column($companyMonth, 'total')[0]);
                            array_push($total, array_column($totalMonth, 'total')[0]);
                        }

                        $totalMonth = DB::select("SELECT count(DISTINCT(la.user_id)) as total from login_activity la left join customer c on la.user_id = c.user_id where c.state = $state_id and month(la.created_at) = $month and year(la.created_at) = $year and la.created_at >= '$date_from' and la.created_at <= '$date_to'");
                        $stateData[] = array_column($totalMonth, 'total')[0];
                    }
                    $dataAllStates[] = $stateData;
                    $allStatesName[] = $value1['name'];
                }
            }
            if ($format == 'Week') {
                foreach ($allStates as $index1 => $value1) {
                    $state_id = $value1['id'];
                    $stateData = array();
                    foreach ($period as $key => $dt) {
                        $break = false;
                        $curr_year = $dt->format('Y');
                        $curr_mon = $dt->format('m');
                        foreach ($weeks as $key1 => $value) {
                            if ($break == true)
                                break;
                            if ($key == 0) {
                                if ($dayOfStartDate >= $value[0] && $dayOfStartDate <= $value[1] == false) {
                                    continue;
                                }
                            }
                            if ($monthOfEndDate == $curr_mon) {
                                if ($dayOfEndDate >= $value[0] && $dayOfEndDate <= $value[1]) {
                                    $break = true;
                                }
                            }
                            if ($index1 == 0) {
                                array_push($label, $dt->format('Y M ') . $weeksLabel[$key1]);

                                $individualWeek = DB::select("SELECT count(DISTINCT(la.user_id)) as total from login_activity la left join customer c on la.user_id = c.user_id where c.isIndividual = 1 and month(la.created_at) = $curr_mon and year(la.created_at) = $curr_year and day(la.created_at) >= $value[0] and day(la.created_at) <= $value[1]");

                                $companyWeek = DB::select("SELECT count(DISTINCT(la.user_id)) as total from login_activity la left join customer c on la.user_id = c.user_id where c.isIndividual = 0 and month(la.created_at) = $curr_mon and year(la.created_at) = $curr_year and day(la.created_at) >= $value[0] and day(la.created_at) <= $value[1]");

                                $totalWeek = DB::select("SELECT count(DISTINCT(la.user_id)) as total from login_activity la left join customer c on la.user_id = c.user_id where month(la.created_at) = $curr_mon and year(la.created_at) = $curr_year and day(la.created_at) >= $value[0] and day(la.created_at) <= $value[1]");

                                array_push($individual, array_column($individualWeek, 'total')[0]);
                                array_push($company, array_column($companyWeek, 'total')[0]);
                                array_push($total, array_column($totalWeek, 'total')[0]);
                            }
                            $totalWeek = DB::select("SELECT count(DISTINCT(la.user_id)) as total from login_activity la left join customer c on la.user_id = c.user_id where  c.state = $state_id and month(la.created_at) = $curr_mon and year(la.created_at) = $curr_year and day(la.created_at) >= $value[0] and day(la.created_at) <= $value[1]");
                            $stateData[] = array_column($totalWeek, 'total')[0];
                        }
                    }
                    $dataAllStates[] = $stateData;
                    $allStatesName[] = $value1['name'];
                }
            }
        }

        $color = Helper::randomColor($allStatesName);
        return array($label, $individual, $company, $total, $dataAllStates, $allStatesName, $color, $tableData);
    }

    public function get_thirty_days_login_district(Request $request)
    {
        $date_from = $request->date_from;
        $date_to = $request->date_to;
        $format = $request->format;
        $state_id = $request->state_id;
        $months = Helper::monthInThisYear();
        $weeks = Helper::dayInWeek();
        $weeksLabel = Helper::weekLabel();
        $curr_mon = date('m');
        $curr_year = date('Y');
        $allCities = City::where('state_id', $state_id)->get()->toArray();
        $labels = array();
        if ($date_from == '' && $date_to == '') {
            foreach ($allCities as $index => $value) {
                $dataMonth = array();
                if ($format == 'Month') {
                    if ($index == 0)
                        $labels = $months[1];
                    foreach ($months[0] as $value1) {
                        $city_id = $value['id'];
                        $dataCities = DB::select("SELECT count(DISTINCT(la.user_id)) as total from login_activity la left join customer c on la.user_id = c.user_id where c.city = $city_id and month(la.created_at) = $value1 and year(la.created_at) = $curr_year ");

                        if ($index == 0) {
                            $dataStates = DB::select("SELECT count(DISTINCT(la.user_id)) as total from login_activity la left join customer c on la.user_id = c.user_id where c.state = $state_id and month(la.created_at) = $value1 and year(la.created_at) = $curr_year ");
                            $dataAll[] = array_column($dataStates, 'total')[0];
                        }
                        $dataMonth[] = array_column($dataCities, 'total')[0];
                    }
                    $cityData[] = $dataMonth;
                    $city[] = $value['name'];
                }
                if ($format == 'Week') {
                    if ($index == 0)
                        $labels = $weeksLabel;
                    foreach ($weeks as $value1) {
                        $city_id = $value['id'];
                        $dataCities = DB::select("SELECT count(DISTINCT(la.user_id)) as total from login_activity la left join customer c on la.user_id = c.user_id where c.city = $city_id and year(la.created_at) = $curr_year and month(la.created_at) = $curr_mon and day(la.created_at) >= $value1[0] and day(la.created_at) <= $value1[1] ");

                        if ($index == 0) {
                            $dataStates = DB::select("SELECT count(DISTINCT(la.user_id)) as total from login_activity la left join customer c on la.user_id = c.user_id where c.state = $state_id and year(la.created_at) = $curr_year and month(la.created_at) = $curr_mon and day(la.created_at) >= $value1[0] and day(la.created_at) <= $value1[1]");
                            $dataAll[] = array_column($dataStates, 'total')[0];
                        }
                        $dataMonth[] = array_column($dataCities, 'total')[0];
                    }
                    $cityData[] = $dataMonth;
                    $city[] = $value['name'];
                }
            }
        } else {
            $start    = (new DateTime($date_from))->modify('first day of this month');
            $end      = (new DateTime($date_to))->modify('first day of next month');
            $interval = DateInterval::createFromDateString('1 month');
            $period   = new DatePeriod($start, $interval, $end);
            $dayOfStartDate = date('d', strtotime($date_from));
            $dayOfEndDate = date('d', strtotime($date_to));
            $monthOfEndDate = date('m', strtotime($date_to));

            foreach ($allCities as $index => $value) {
                $dataMonth = array();
                if ($format == 'Month') {
                    foreach ($period as $key => $dt) {
                        $year = $dt->format('Y');
                        $month = $dt->format('m');

                        $city_id = $value['id'];
                        $dataCities = DB::select("SELECT count(DISTINCT(la.user_id)) as total from login_activity la left join customer c on la.user_id = c.user_id where c.city = $city_id and la.created_at >= '$date_from' and la.created_at <= '$date_to' and month(la.created_at) = $month and year(la.created_at) = $year");

                        if ($index == 0) {
                            array_push($labels, $dt->format('M Y'));
                            $dataStates = DB::select("SELECT count(DISTINCT(la.user_id)) as total from login_activity la left join customer c on la.user_id = c.user_id where c.state = $state_id and month(la.created_at) = $month and year(la.created_at) = $year and la.created_at >= '$date_from' and la.created_at <= '$date_to'");
                            $dataAll[] = array_column($dataStates, 'total')[0];
                        }
                        $dataMonth[] = array_column($dataCities, 'total')[0];
                    }
                    $cityData[] = $dataMonth;
                    $city[] = $value['name'];
                }
                if ($format == 'Week') {
                    foreach ($period as $key => $dt) {
                        $break = false;
                        $curr_year = $dt->format('Y');
                        $curr_mon = $dt->format('m');
                        foreach ($weeks as $key1 => $value1) {
                            if ($break == true)
                                break;
                            if ($key == 0) {
                                if ($dayOfStartDate >= $value1[0] && $dayOfStartDate <= $value1[1] == false) {
                                    continue;
                                }
                            }
                            if ($monthOfEndDate == $curr_mon) {
                                if ($dayOfEndDate >= $value1[0] && $dayOfEndDate <= $value1[1]) {
                                    $break = true;
                                }
                            }
                            $city_id = $value['id'];
                            $dataCities = DB::select("SELECT count(DISTINCT(la.user_id)) as total from login_activity la left join customer c on la.user_id = c.user_id where c.city = $city_id and year(la.created_at) = $curr_year and month(la.created_at) = $curr_mon and day(la.created_at) >= $value1[0] and day(la.created_at) <= $value1[1] ");

                            if ($index == 0) {
                                array_push($labels, $dt->format('Y M ') . $weeksLabel[$key1]);
                                $dataStates = DB::select("SELECT count(DISTINCT(la.user_id)) as total from login_activity la left join customer c on la.user_id = c.user_id where c.state = $state_id and year(la.created_at) = $curr_year and month(la.created_at) = $curr_mon and day(la.created_at) >= $value1[0] and day(la.created_at) <= $value1[1]");
                                $dataAll[] = array_column($dataStates, 'total')[0];
                            }
                            $dataMonth[] = array_column($dataCities, 'total')[0];
                        }
                    }
                    $cityData[] = $dataMonth;
                    $city[] = $value['name'];
                }
            }
        }
        $colors = Helper::randomColor($allCities);
        $array = array($city, $colors, $cityData, $labels, $dataAll);
        return $array;
    }

    public function get_active_transaction(Request $request)
    {
        $date_from = $request->date_from;
        $date_to = $request->date_to;
        $curr_mon = date('m');
        $curr_year = date('Y');
        $format = $request->format;
        $months = Helper::monthInThisYear();
        $weeks = Helper::dayInWeek();
        $weeksLabel = Helper::weekLabel();
        $allStates = State::get()->toArray();
        $allStatesName = array();

        $individual = array();
        $company = array();
        $total = array();
        $labels = array();
        $tableData = array();

        if ($date_from == '' && $date_to == '') {
            $tableData = DB::select("SELECT DISTINCT(c.email),IF(c.isIndividual=1, 'B2C', 'B2B') as type, concat(year(co.created_at),'-',month(co.created_at)) as date from collection co left join customer c on c.id = co.customer_id where co.status = 1");
            if ($format == 'Month') {
                $labels = $months[1];
                foreach ($allStates as $index1 => $value1) {
                    $state_id = $value1['id'];
                    $stateData = array();
                    foreach ($months[0] as  $index => $value) {
                        if ($index1 == 0) {
                            $individualMonth = DB::select("SELECT count(DISTINCT(co.customer_id)) as total from collection co left join customer c on co.customer_id = c.id where c.isIndividual = 1 and month(co.created_at) = $value and co.status = 1 and year(co.created_at) = $curr_year");

                            $companyMonth = DB::select("SELECT count(DISTINCT(co.customer_id)) as total from collection co left join customer c on co.customer_id = c.id where c.isIndividual = 0 and month(co.created_at) = $value and co.status = 1 and year(co.created_at) = $curr_year");

                            $totalMonth = DB::select("SELECT count(DISTINCT(co.customer_id)) as total from collection co left join customer c on co.customer_id = c.id where month(co.created_at) = $value and co.status = 1 and year(co.created_at) = $curr_year");

                            array_push($individual, array_column($individualMonth, 'total')[0]);
                            array_push($company, array_column($companyMonth, 'total')[0]);
                            array_push($total, array_column($totalMonth, 'total')[0]);
                        }

                        $totalMonth = DB::select("SELECT count(DISTINCT(co.customer_id)) as total from collection co left join customer c on co.customer_id = c.id where c.state = $state_id and month(co.created_at) = $value and co.status = 1 and year(co.created_at) = $curr_year");
                        $stateData[] = array_column($totalMonth, 'total')[0];
                    }
                    $dataAllStates[] = $stateData;
                    $allStatesName[] = $value1['name'];
                }
            }
            if ($format == 'Week') {
                $labels = $weeksLabel;
                foreach ($allStates as $index1 => $value1) {
                    $state_id = $value1['id'];
                    $stateData = array();
                    foreach ($weeks as $value) {
                        if ($index1 == 0) {
                            $individualWeek = DB::select("SELECT count(DISTINCT(co.customer_id)) as total from collection co left join customer c on co.customer_id = c.id where c.isIndividual = 1 and month(co.created_at) = $curr_mon and co.status = 1 and year(co.created_at) = $curr_year and day(co.created_at) >= $value[0] and day(co.created_at) <= $value[1]");

                            $companyWeek = DB::select("SELECT count(DISTINCT(co.customer_id)) as total from collection co left join customer c on co.customer_id = c.id where c.isIndividual = 0 and month(co.created_at) = $curr_mon and co.status = 1 and year(co.created_at) = $curr_year and day(co.created_at) >= $value[0] and day(co.created_at) <= $value[1]");

                            $totalWeek = DB::select("SELECT count(DISTINCT(co.customer_id)) as total from collection co left join customer c on co.customer_id = c.id where month(co.created_at) = $curr_mon and co.status = 1 and year(co.created_at) = $curr_year and day(co.created_at) >= $value[0] and day(co.created_at) <= $value[1]");

                            array_push($individual, array_column($individualWeek, 'total')[0]);
                            array_push($company, array_column($companyWeek, 'total')[0]);
                            array_push($total, array_column($totalWeek, 'total')[0]);
                        }
                        $totalWeek = DB::select("SELECT count(DISTINCT(co.customer_id)) as total from collection co left join customer c on co.customer_id = c.id where  c.state = $state_id and c.isIndividual = 1 and month(co.created_at) = $curr_mon and co.status = 1 and year(co.created_at) = $curr_year and day(co.created_at) >= $value[0] and day(co.created_at) <= $value[1]");
                        $stateData[] = array_column($totalWeek, 'total')[0];
                    }
                    $dataAllStates[] = $stateData;
                    $allStatesName[] = $value1['name'];
                }
            }
        } else {
            $start    = (new DateTime($date_from))->modify('first day of this month');
            $end      = (new DateTime($date_to))->modify('first day of next month');
            $interval = DateInterval::createFromDateString('1 month');
            $period   = new DatePeriod($start, $interval, $end);
            $dayOfStartDate = date('d', strtotime($date_from));
            $dayOfEndDate = date('d', strtotime($date_to));
            $monthOfEndDate = date('m', strtotime($date_to));
            $labels = array();
            $tableData = DB::select("SELECT DISTINCT(c.email),IF(c.isIndividual=1, 'B2C', 'B2B') as type, concat(year(co.created_at),'-',month(co.created_at)) as date from collection co left join customer c on c.id = co.customer_id where co.created_at >= '$date_from' and co.status = 1 and co.created_at <= '$date_to'");
            if ($format == 'Month') {
                foreach ($allStates as $index1 => $value1) {
                    $state_id = $value1['id'];
                    $stateData = array();
                    foreach ($period as $key => $dt) {
                        $year = $dt->format('Y');
                        $month = $dt->format('m');

                        if ($index1 == 0) {
                            array_push($labels, $dt->format('M Y'));

                            $individualMonth = DB::select("SELECT count(DISTINCT(co.customer_id)) as total from collection co left join customer c on co.customer_id = c.id where c.isIndividual = 1 and month(co.created_at) = $month and year(co.created_at) = $year and co.status = 1 and co.created_at >= '$date_from' and co.created_at <= '$date_to'");

                            $companyMonth = DB::select("SELECT count(DISTINCT(co.customer_id)) as total from collection co left join customer c on co.customer_id = c.id where c.isIndividual = 0 and month(co.created_at) = $month and year(co.created_at) = $year and co.status = 1 and co.created_at >= '$date_from' and co.created_at <= '$date_to'");

                            $totalMonth = DB::select("SELECT count(DISTINCT(co.customer_id)) as total from collection co left join customer c on co.customer_id = c.id where month(co.created_at) = $month and year(co.created_at) = $year and co.status = 1 and co.created_at >= '$date_from' and co.created_at <= '$date_to'");

                            array_push($individual, array_column($individualMonth, 'total')[0]);
                            array_push($company, array_column($companyMonth, 'total')[0]);
                            array_push($total, array_column($totalMonth, 'total')[0]);
                        }

                        $totalMonth = DB::select("SELECT count(DISTINCT(co.customer_id)) as total from collection co left join customer c on co.customer_id = c.id where c.state = $state_id and month(co.created_at) = $month and year(co.created_at) = $year and co.status = 1 and co.created_at >= '$date_from' and co.created_at <= '$date_to'");
                        $stateData[] = array_column($totalMonth, 'total')[0];
                    }
                    $dataAllStates[] = $stateData;
                    $allStatesName[] = $value1['name'];
                }
            }
            if ($format == 'Week') {
                foreach ($allStates as $index1 => $value1) {
                    $state_id = $value1['id'];
                    $stateData = array();
                    foreach ($period as $key => $dt) {
                        $break = false;
                        $curr_year = $dt->format('Y');
                        $curr_mon = $dt->format('m');
                        foreach ($weeks as $key1 => $value) {
                            if ($break == true)
                                break;
                            if ($key == 0) {
                                if ($dayOfStartDate >= $value[0] && $dayOfStartDate <= $value[1] == false) {
                                    continue;
                                }
                            }
                            if ($monthOfEndDate == $curr_mon) {
                                if ($dayOfEndDate >= $value[0] && $dayOfEndDate <= $value[1]) {
                                    $break = true;
                                }
                            }
                            if ($index1 == 0) {
                                array_push($labels, $dt->format('Y M ') . $weeksLabel[$key1]);

                                $individualWeek = DB::select("SELECT count(DISTINCT(co.customer_id)) as total from collection co left join customer c on co.customer_id = c.id where c.isIndividual = 1 and month(co.created_at) = $curr_mon and co.status = 1 and year(co.created_at) = $curr_year and day(co.created_at) >= $value[0] and day(co.created_at) <= $value[1]");

                                $companyWeek = DB::select("SELECT count(DISTINCT(co.customer_id)) as total from collection co left join customer c on co.customer_id = c.id where c.isIndividual = 0 and month(co.created_at) = $curr_mon and co.status = 1 and year(co.created_at) = $curr_year and day(co.created_at) >= $value[0] and day(co.created_at) <= $value[1]");

                                $totalWeek = DB::select("SELECT count(DISTINCT(co.customer_id)) as total from collection co left join customer c on co.customer_id = c.id where month(co.created_at) = $curr_mon and year(co.created_at) = $curr_year and co.status = 1 and day(co.created_at) >= $value[0] and day(co.created_at) <= $value[1]");

                                array_push($individual, array_column($individualWeek, 'total')[0]);
                                array_push($company, array_column($companyWeek, 'total')[0]);
                                array_push($total, array_column($totalWeek, 'total')[0]);
                            }
                            $totalWeek = DB::select("SELECT count(DISTINCT(co.customer_id)) as total from collection co left join customer c on co.customer_id = c.id where  c.state = $state_id and c.isIndividual = 1 and month(co.created_at) = $curr_mon and co.status = 1 and year(co.created_at) = $curr_year and day(co.created_at) >= $value[0] and day(co.created_at) <= $value[1]");
                            $stateData[] = array_column($totalWeek, 'total')[0];
                        }
                    }
                    $dataAllStates[] = $stateData;
                    $allStatesName[] = $value1['name'];
                }
            }
        }

        $color = Helper::randomColor($allStatesName);
        return array($labels, $individual, $company, $total, $dataAllStates, $allStatesName, $color, $tableData);
    }

    public function get_active_transaction_district(Request $request)
    {
        $date_from = $request->date_from;
        $date_to = $request->date_to;
        $format = $request->format;
        $state_id = $request->state_id;
        $months = Helper::monthInThisYear();
        $weeks = Helper::dayInWeek();
        $weeksLabel = Helper::weekLabel();
        $curr_mon = date('m');
        $curr_year = date('Y');
        $allCities = City::where('state_id', $state_id)->get()->toArray();
        $labels = array();
        if ($date_from == '' && $date_to == '') {
            foreach ($allCities as $index => $value) {
                $dataMonth = array();
                if ($format == 'Month') {
                    if ($index == 0)
                        $labels = $months[1];
                    foreach ($months[0] as $value1) {
                        $city_id = $value['id'];
                        $dataCities = DB::select("SELECT count(DISTINCT(co.customer_id)) as total from collection co left join customer c on co.customer_id = c.id where c.city = $city_id and month(co.created_at) = $value1 and co.status = 1 and year(co.created_at) = $curr_year  ");

                        if ($index == 0) {
                            $dataStates = DB::select("SELECT count(DISTINCT(co.customer_id)) as total from collection co left join customer c on co.customer_id = c.id where c.state = $state_id and month(co.created_at) = $value1 and co.status = 1 and year(co.created_at) = $curr_year ");
                            $dataAll[] = array_column($dataStates, 'total')[0];
                        }
                        $dataMonth[] = array_column($dataCities, 'total')[0];
                    }
                    $cityData[] = $dataMonth;
                    $city[] = $value['name'];
                }
                if ($format == 'Week') {
                    if ($index == 0)
                        $labels = $weeksLabel;
                    foreach ($weeks as $value1) {
                        $city_id = $value['id'];
                        $dataCities = DB::select("SELECT count(DISTINCT(co.customer_id)) as total from collection co left join customer c on co.customer_id = c.id where c.city = $city_id and year(co.created_at) = $curr_year and co.status = 1 and month(co.created_at) = $curr_mon and day(co.created_at) >= $value1[0] and day(co.created_at) <= $value1[1] ");

                        if ($index == 0) {
                            $dataStates = DB::select("SELECT count(DISTINCT(co.customer_id)) as total from collection co left join customer c on co.customer_id = c.id where c.state = $state_id and year(co.created_at) = $curr_year and co.status = 1 and month(co.created_at) = $curr_mon and day(co.created_at) >= $value1[0] and day(co.created_at) <= $value1[1]");
                            $dataAll[] = array_column($dataStates, 'total')[0];
                        }
                        $dataMonth[] = array_column($dataCities, 'total')[0];
                    }
                    $cityData[] = $dataMonth;
                    $city[] = $value['name'];
                }
            }
        } else {
            $start    = (new DateTime($date_from))->modify('first day of this month');
            $end      = (new DateTime($date_to))->modify('first day of next month');
            $interval = DateInterval::createFromDateString('1 month');
            $period   = new DatePeriod($start, $interval, $end);
            $dayOfStartDate = date('d', strtotime($date_from));
            $dayOfEndDate = date('d', strtotime($date_to));
            $monthOfEndDate = date('m', strtotime($date_to));

            foreach ($allCities as $index => $value) {
                $dataMonth = array();
                if ($format == 'Month') {
                    foreach ($period as $key => $dt) {
                        $year = $dt->format('Y');
                        $month = $dt->format('m');

                        $city_id = $value['id'];
                        $dataCities = DB::select("SELECT count(DISTINCT(co.customer_id)) as total from collection co left join customer c on co.customer_id = c.id where c.city = $city_id and co.created_at >= '$date_from' and co.created_at <= '$date_to' and co.status = 1 and month(co.created_at) = $month and year(co.created_at) = $year  ");

                        if ($index == 0) {
                            array_push($labels, $dt->format('M Y'));
                            $dataStates = DB::select("SELECT count(DISTINCT(co.customer_id)) as total from collection co left join customer c on co.customer_id = c.id where c.state = $state_id and month(co.created_at) = $month and year(co.created_at) = $year and co.status = 1 and co.created_at >= '$date_from' and co.created_at <= '$date_to'");
                            $dataAll[] = array_column($dataStates, 'total')[0];
                        }
                        $dataMonth[] = array_column($dataCities, 'total')[0];
                    }
                    $cityData[] = $dataMonth;
                    $city[] = $value['name'];
                }
                if ($format == 'Week') {
                    foreach ($period as $key => $dt) {
                        $break = false;
                        $curr_year = $dt->format('Y');
                        $curr_mon = $dt->format('m');
                        foreach ($weeks as $key1 => $value1) {
                            if ($break == true)
                                break;
                            if ($key == 0) {
                                if ($dayOfStartDate >= $value1[0] && $dayOfStartDate <= $value1[1] == false) {
                                    continue;
                                }
                            }
                            if ($monthOfEndDate == $curr_mon) {
                                if ($dayOfEndDate >= $value1[0] && $dayOfEndDate <= $value1[1]) {
                                    $break = true;
                                }
                            }
                            $city_id = $value['id'];
                            $dataCities = DB::select("SELECT count(DISTINCT(co.customer_id)) as total from collection co left join customer c on co.customer_id = c.id where c.city = $city_id and year(co.created_at) = $curr_year and month(co.created_at) = $curr_mon and co.status = 1 and day(co.created_at) >= $value1[0] and day(c.created_at) <= $value1[1] ");

                            if ($index == 0) {
                                array_push($labels, $dt->format('Y M ') . $weeksLabel[$key1]);
                                $dataStates = DB::select("SELECT count(DISTINCT(co.customer_id)) as total from collection co left join customer c on co.customer_id = c.id where c.state = $state_id and year(co.created_at) = $curr_year and month(co.created_at) = $curr_mon and co.status = 1 and day(co.created_at) >= $value1[0] and day(co.created_at) <= $value1[1]");
                                $dataAll[] = array_column($dataStates, 'total')[0];
                            }
                            $dataMonth[] = array_column($dataCities, 'total')[0];
                        }
                    }
                    $cityData[] = $dataMonth;
                    $city[] = $value['name'];
                }
            }
        }
        $colors = Helper::randomColor($allCities);
        $array = array($city, $colors, $cityData, $labels, $dataAll);
        return $array;
    }

    public function get_membership_tier()
    {
        $tier = DB::select("SELECT count(cm.customer_id) as total from customer_membership cm right join level l on cm.level_id = l.id group by l.id order by l.id asc");
        $tier = array_column($tier, 'total');
        $level = Level::orderBy('id')->get()->toArray();
        $level =  array_column($level, 'name');
        $color = Helper::randomColor($level);
        $tableData = DB::select("SELECT c.email, l.name from customer_membership cm left join customer c on cm.customer_id = c.id left join level l on l.id = cm.level_id");

        return array($tier, $level, $color, $tableData);
    }

    public function get_user_preference()
    {
        $numberOfCustomer = DB::select("SELECT count(crc.customer_id) as total from customer_reward_category crc right join reward_category rc on crc.reward_category_id = rc.id group by rc.id order by rc.id asc");
        $numberOfCustomer = array_column($numberOfCustomer, 'total');
        $category = RewardsCategory::orderBy('id')->get()->toArray();
        $category =  array_column($category, 'name');
        $color = Helper::randomColor($category);
        $tableData = DB::select("SELECT c.email, rc.name from customer_reward_category crc left join customer c on crc.customer_id = c.id left join reward_category rc on crc.reward_category_id = rc.id order by rc.id asc");

        return array($numberOfCustomer, $category, $color, $tableData);
    }

    public function growth_and_population()
    {
        $allStates  = State::get()->toArray();
        return view('report.app_performance.growth_and_population', compact('allStates'));
    }

    public function reward_performance()
    {
        $states = State::get();
        return view('report.reward_performance', compact('states'));
    }

    public function get_reward_performance_data(Request $request)
    {
        $labels = array();
        $date_from = $request->date_from;
        $date_to = $request->date_to;
        $curr_mon = date('m');
        $curr_year = date('Y');
        $format = $request->format;
        $months = Helper::monthInThisYear();
        $tableData = array();
        if ($date_from == '' && $date_to == '') {
            $tableData = DB::select("SELECT c.email, sum(cr.point_used) as total from customer_reward cr left join customer c on c.id = cr.customer_id group by cr.customer_id,c.email");
            if ($format == 'Month') {
                $monthForOnSite = DB::select("SELECT sum(point_used) as total, month(redeem_date) as month FROM customer_reward left join customer on customer_reward.customer_id = customer.id where year(redeem_date) = $curr_year group by month(redeem_date) order by month(redeem_date) asc");

                $companyMonth = DB::select("SELECT sum(point_used) as total, month(redeem_date) as month FROM customer_reward left join customer on customer_reward.customer_id = customer.id where customer.isIndividual = 0 and year(redeem_date) = $curr_year group by month(redeem_date) order by month(redeem_date) asc");

                $individualMonth = DB::select("SELECT sum(point_used) as total, month(redeem_date) as month FROM customer_reward left join customer on customer_reward.customer_id = customer.id where customer.isIndividual = 1 and year(redeem_date) = $curr_year group by month(redeem_date) order by month(redeem_date) asc");

                $individualMonthLabel = array_column($individualMonth, 'month');
                $companyMonthLabel = array_column($companyMonth, 'month');
                $monthForOnSiteLabel = array_column($monthForOnSite, 'month');
                $labels  = $months[0];
            } else if ($format == 'Week') {
                $weeks = Helper::dayInWeek();
                $weekLabel = Helper::weekLabel();
                $labels = $weekLabel;
                $monthForOnSite = array();
                foreach ($weeks as $value) {
                    $weekData = DB::select("SELECT IFNULL(sum(point_used), 0) as total FROM customer_reward left join customer on customer_reward.customer_id = customer.id where day(redeem_date) >= $value[0] and day(redeem_date) <= $value[1] and month(redeem_date) = $curr_mon and year(redeem_date) =  YEAR(CURRENT_DATE())");
                    $companyWeekData = DB::select("SELECT IFNULL(sum(point_used), 0) as total FROM customer_reward left join customer on customer_reward.customer_id = customer.id where customer.isIndividual = 0 and day(redeem_date) >= $value[0] and day(redeem_date) <= $value[1] and month(redeem_date) = $curr_mon and year(redeem_date) =  YEAR(CURRENT_DATE())");
                    $individualWeekData = DB::select("SELECT IFNULL(sum(point_used), 0) as total FROM customer_reward left join customer on customer_reward.customer_id = customer.id where customer.isIndividual = 1 and day(redeem_date) >= $value[0] and day(redeem_date) <= $value[1] and month(redeem_date) = $curr_mon and year(redeem_date) =  YEAR(CURRENT_DATE())");

                    $monthForOnSite[] = array_column($weekData, 'total')[0];
                    $companyMonth[] = array_column($companyWeekData, 'total')[0];
                    $individualMonth[] = array_column($individualWeekData, 'total')[0];
                }
            }
            $allStates = State::get()->toArray();
            $allStatesName = array();

            foreach ($allStates as $value) {
                if ($format == 'Month') {
                    $transactionMonthAll = DB::select("SELECT sum(point_used) as total, month(redeem_date) as month FROM customer_reward left join customer on customer_reward.customer_id = customer.id where customer.state = ? and year(redeem_date) = $curr_year group by month(redeem_date) order by month(redeem_date) asc", array($value['id']));
                } else if ($format == 'Week') {
                    $transactionMonthAll = array();
                    $redemptionByWeekInCategory = array();

                    foreach ($weeks as $value1) {
                        $transactionWeekly = DB::select("SELECT IFNULL(sum(point_used), 0) as total FROM customer_reward left join customer on customer_reward.customer_id = customer.id where day(redeem_date) >= $value1[0] and day(redeem_date) <= $value1[1] and month(redeem_date) = $curr_mon and year(redeem_date) =  YEAR(CURRENT_DATE()) and customer.state = ?", array($value['id']));
                        $transactionWeekly = array_column($transactionWeekly, 'total')[0];
                        array_push($transactionMonthAll, $transactionWeekly);
                        $redemptionByEachWeekInCategory = DB::select("SELECT count(B.id) as total FROM (SELECT customer_reward.id, customer_reward.reward_id from customer_reward where month(redeem_date) = $curr_mon and year(redeem_date) =  YEAR(CURRENT_DATE()) AND day(redeem_date) >= $value1[0] AND day(redeem_date) <= $value1[1]) AS B RIGHT JOIN rewards ON B.reward_id = rewards.id RIGHT JOIN reward_category on rewards.reward_category_id = reward_category.id group by reward_category.id order by reward_category.id");

                        array_push($redemptionByWeekInCategory, array_column($redemptionByEachWeekInCategory, 'total'));
                    }
                }
                $dataMonth[] = $transactionMonthAll;

                $allStatesName[] = $value['name'];
            }

            if ($format == 'Month') {

                $data_only = array();

                foreach ($dataMonth as $keyMonth => $month) {
                    foreach ($labels as $key => $value) {
                        $tempMonth = array_column($month, 'month');
                        if (!in_array($value, $tempMonth)) {
                            $dataMonth[$keyMonth][] = json_decode(json_encode(array('total' => 0, 'month' => $value)));
                        }
                        usort($dataMonth[$keyMonth], function ($a, $b) {
                            return $a->month <=> $b->month;
                        });
                    }
                    $temp = array_column($dataMonth[$keyMonth], 'total');
                    $data_only[] = $temp;
                }

                $redemptionByMonthInCategory = array();
                foreach ($labels as $key => $value) {
                    if (!in_array($value, $individualMonthLabel)) {
                        array_push($individualMonth, json_decode(json_encode(array('total' => 0, 'month' => $value))));
                    }
                    if (!in_array($value, $companyMonthLabel)) {
                        array_push($companyMonth, json_decode(json_encode(array('total' => 0, 'month' => $value))));
                    }
                    if (!in_array($value, $monthForOnSiteLabel)) {
                        array_push($monthForOnSite, json_decode(json_encode(array('total' => 0, 'month' => $value))));
                    }

                    $redemptionByEachMonthInCategory = DB::select("SELECT count(B.id) as total FROM (SELECT customer_reward.id, customer_reward.reward_id from customer_reward where month(redeem_date) = $value and year(redeem_date) = $curr_year) AS B RIGHT JOIN rewards ON B.reward_id = rewards.id RIGHT JOIN reward_category on rewards.reward_category_id = reward_category.id group by reward_category.id order by reward_category.id");

                    array_push($redemptionByMonthInCategory, array_column($redemptionByEachMonthInCategory, 'total'));
                }
                usort($companyMonth, function ($a, $b) {
                    return $a->month <=> $b->month;
                });

                usort($individualMonth, function ($a, $b) {
                    return $a->month <=> $b->month;
                });

                usort($monthForOnSite, function ($a, $b) {
                    return $a->month <=> $b->month;
                });
                $labels = $months[1];
            }
        } else {
            $start    = (new DateTime($date_from))->modify('first day of this month');
            $end      = (new DateTime($date_to))->modify('first day of next month');
            $interval = DateInterval::createFromDateString('1 month');
            $period   = new DatePeriod($start, $interval, $end);
            $dayOfStartDate = date('d', strtotime($date_from));
            $dayOfEndDate = date('d', strtotime($date_to));
            $monthOfEndDate = date('m', strtotime($date_to));

            $tableData = DB::select("SELECT c.email, sum(cr.point_used) as total from customer_reward cr left join customer c on c.id = cr.customer_id where cr.redeem_date >= '$date_from' and (redeem_date) <= '$date_to' group by cr.customer_id, c.email");
            if ($format == 'Month') {
                $redemptionByWeekInCategory = array();
                foreach ($period as $key => $dt) {
                    $year = $dt->format('Y');
                    $month = $dt->format('m');

                    array_push($labels, $dt->format('M Y'));
                    $weekData = DB::select("SELECT IFNULL(sum(point_used), 0) as total FROM customer_reward left join customer on customer_reward.customer_id = customer.id where (redeem_date) >= '$date_from' and (redeem_date) <= '$date_to' and month(redeem_date) = $month and year(redeem_date) = $year");
                    $companyWeekData = DB::select("SELECT IFNULL(sum(point_used), 0) as total FROM customer_reward left join customer on customer_reward.customer_id = customer.id where customer.isIndividual = 0 and (redeem_date) >= '$date_from' and (redeem_date) <= '$date_to' and month(redeem_date) = $month and year(redeem_date) = $year");
                    $individualWeekData = DB::select("SELECT IFNULL(sum(point_used), 0) as total FROM customer_reward left join customer on customer_reward.customer_id = customer.id where customer.isIndividual = 1 and (redeem_date) >= '$date_from' and (redeem_date) <= '$date_to' and month(redeem_date) = $month and year(redeem_date) = $year");
                    $redemptionByEachWeekInCategory = DB::select("SELECT count(B.id) as total FROM (SELECT customer_reward.id, customer_reward.reward_id from customer_reward where month(redeem_date) = $month and year(redeem_date) = $year AND (redeem_date) >= '$date_from' AND (redeem_date) <= '$date_to') AS B RIGHT JOIN rewards ON B.reward_id = rewards.id RIGHT JOIN reward_category on rewards.reward_category_id = reward_category.id group by reward_category.id order by reward_category.id");

                    array_push($redemptionByWeekInCategory, array_column($redemptionByEachWeekInCategory, 'total'));

                    $monthForOnSite[] = array_column($weekData, 'total')[0];
                    $companyMonth[] = array_column($companyWeekData, 'total')[0];
                    $individualMonth[] = array_column($individualWeekData, 'total')[0];
                }
            } else if ($format == 'Week') {
                $weeks = Helper::dayInWeek();
                $weekLabel = Helper::weekLabel();
                $monthForOnSite = array();
                $companyMonth = array();
                $individualMonth = array();

                foreach ($period as $key => $dt) {
                    $break = false;
                    $curr_year = $dt->format('Y');
                    $curr_mon = $dt->format('m');
                    foreach ($weeks as $key1 => $value) {
                        if ($break == true)
                            break;
                        if ($key == 0) {
                            if ($dayOfStartDate >= $value[0] && $dayOfStartDate <= $value[1] == false) {
                                continue;
                            }
                        }
                        if ($monthOfEndDate == $curr_mon) {
                            if ($dayOfEndDate >= $value[0] && $dayOfEndDate <= $value[1]) {
                                $break = true;
                            }
                        }
                        array_push($labels, $dt->format('Y M ') . $weekLabel[$key1]);
                        $weekData = DB::select("SELECT IFNULL(sum(point_used), 0) as total FROM customer_reward left join customer on customer_reward.customer_id = customer.id where day(redeem_date) >= $value[0] and day(redeem_date) <= $value[1] and month(redeem_date) = $curr_mon and year(redeem_date) = $curr_year");
                        $companyWeekData = DB::select("SELECT IFNULL(sum(point_used), 0) as total FROM customer_reward left join customer on customer_reward.customer_id = customer.id where customer.isIndividual = 0 and day(redeem_date) >= $value[0] and day(redeem_date) <= $value[1] and month(redeem_date) = $curr_mon and year(redeem_date) =  $curr_year");
                        $individualWeekData = DB::select("SELECT IFNULL(sum(point_used), 0) as total FROM customer_reward left join customer on customer_reward.customer_id = customer.id where customer.isIndividual = 1 and day(redeem_date) >= $value[0] and day(redeem_date) <= $value[1] and month(redeem_date) = $curr_mon and year(redeem_date) =  $curr_year");

                        $monthForOnSite[] = array_column($weekData, 'total')[0];
                        $companyMonth[] = array_column($companyWeekData, 'total')[0];
                        $individualMonth[] = array_column($individualWeekData, 'total')[0];
                    }
                }
            }
            $allStates = State::get()->toArray();
            $allStatesName = array();

            foreach ($allStates as $value) {
                $transactionMonthAll = array();
                if ($format == 'Month') {

                    foreach ($period as $key => $dt) {
                        $year = $dt->format('Y');
                        $month = $dt->format('m');
                        $transactionWeekly = DB::select("SELECT IFNULL(sum(point_used), 0) as total FROM customer_reward left join customer on customer_reward.customer_id = customer.id where (redeem_date) >='$date_from' and (redeem_date) <= '$date_to' and month(redeem_date) = $month and year(redeem_date) =  $year and customer.state = ?", array($value['id']));
                        $transactionWeekly = array_column($transactionWeekly, 'total')[0];
                        array_push($transactionMonthAll, $transactionWeekly);
                    }
                    $dataMonth[] = $transactionMonthAll;
                } else if ($format == 'Week') {
                    $transactionMonthAll = array();
                    $redemptionByWeekInCategory = array();

                    foreach ($period as $key => $dt) {
                        $break = false;
                        $curr_year = $dt->format('Y');
                        $curr_mon = $dt->format('m');
                        foreach ($weeks as $key1 => $value1) {
                            if ($break == true)
                                break;
                            if ($key == 0) {
                                if ($dayOfStartDate >= $value1[0] && $dayOfStartDate <= $value1[1] == false) {
                                    continue;
                                }
                            }
                            if ($monthOfEndDate == $curr_mon) {
                                if ($dayOfEndDate >= $value1[0] && $dayOfEndDate <= $value1[1]) {
                                    $break = true;
                                }
                            }
                            $transactionWeekly = DB::select("SELECT IFNULL(sum(point_used), 0) as total FROM customer_reward left join customer on customer_reward.customer_id = customer.id where day(redeem_date) >= $value1[0] and day(redeem_date) <= $value1[1] and month(redeem_date) = $curr_mon and year(redeem_date) =  $curr_year and customer.state = ?", array($value['id']));
                            $transactionWeekly = array_column($transactionWeekly, 'total')[0];
                            array_push($transactionMonthAll, $transactionWeekly);
                            $redemptionByEachWeekInCategory = DB::select("SELECT count(B.id) as total FROM (SELECT customer_reward.id, customer_reward.reward_id from customer_reward where month(redeem_date) = $curr_mon and year(redeem_date) = $curr_year AND day(redeem_date) >= $value1[0] AND day(redeem_date) <= $value1[1]) AS B RIGHT JOIN rewards ON B.reward_id = rewards.id RIGHT JOIN reward_category on rewards.reward_category_id = reward_category.id group by reward_category.id order by reward_category.id");

                            array_push($redemptionByWeekInCategory, array_column($redemptionByEachWeekInCategory, 'total'));
                        }
                    }
                    $dataMonth[] = $transactionMonthAll;
                }

                $allStatesName[] = $value['name'];
            }
        }


        $userMonth = array_column($monthForOnSite, 'total');
        $categoryLabel = DB::select("SELECT name from reward_category order by reward_category.id");
        $categoryLabel = array_column($categoryLabel, 'name');

        $colors = Helper::randomColor($allStates);
        $colors_category = Helper::randomColor($categoryLabel);

        if ($date_from != '' && $date_to != '') {
            return array($companyMonth, $individualMonth, $monthForOnSite, $labels, $allStatesName, $dataMonth, $colors, $categoryLabel, $redemptionByWeekInCategory, $tableData);
        }
        $companyMonth = array_column($companyMonth, 'total');
        $individualMonth = array_column($individualMonth, 'total');
        if ($format == 'Month') {
            return array($companyMonth, $individualMonth, $userMonth, $labels,  $allStatesName, $data_only, $colors, $userMonth, $categoryLabel, $redemptionByMonthInCategory, $colors_category, $tableData);
        }
        if ($format == 'Week') {
            return array($companyMonth, $individualMonth, $monthForOnSite, $weekLabel, $allStatesName, $dataMonth, $colors, $categoryLabel, $redemptionByWeekInCategory, $tableData);
        }
    }

    public function get_reward_performance_data_get($format, $state1, $state2)
    {
        $curr_mon = date('m');
        $curr_year = date('y');
        $months = Helper::monthInThisYear();

        if ($format == 'Month') {
            $monthForOnSite = DB::select("SELECT sum(point_used) as total, month(redeem_date) as month FROM customer_reward left join customer on customer_reward.customer_id = customer.id group by month(redeem_date) order by month(redeem_date) asc");

            $companyMonth = DB::select("SELECT sum(point_used) as total, month(redeem_date) as month FROM customer_reward left join customer on customer_reward.customer_id = customer.id where customer.isIndividual = 0 group by month(redeem_date) order by month(redeem_date) asc");

            $individualMonth = DB::select("SELECT sum(point_used) as total, month(redeem_date) as month FROM customer_reward left join customer on customer_reward.customer_id = customer.id where customer.isIndividual = 1 group by month(redeem_date) order by month(redeem_date) asc");

            $individualMonthLabel = array_column($individualMonth, 'month');
            $companyMonthLabel = array_column($companyMonth, 'month');
            $labels  = array_column($monthForOnSite, 'month');
        } else if ($format == 'Week') {
            $weeks = Helper::dayInWeek();
            $weekLabel = Helper::weekLabel();
            $monthForOnSite = array();
            foreach ($weeks as $value) {
                $weekData = DB::select("SELECT IFNULL(sum(point_used), 0) as total FROM customer_reward left join customer on customer_reward.customer_id = customer.id where day(redeem_date) >= $value[0] and day(redeem_date) <= $value[1] and month(redeem_date) = $curr_mon and year(redeem_date) =  YEAR(CURRENT_DATE())");
                $companyWeekData = DB::select("SELECT IFNULL(sum(point_used), 0) as total FROM customer_reward left join customer on customer_reward.customer_id = customer.id where customer.isIndividual = 0 and day(redeem_date) >= $value[0] and day(redeem_date) <= $value[1] and month(redeem_date) = $curr_mon and YEAR(redeem_date) =  YEAR(CURRENT_DATE())");
                $individualWeekData = DB::select("SELECT IFNULL(sum(point_used), 0) as total FROM customer_reward left join customer on customer_reward.customer_id = customer.id where customer.isIndividual = 1 and day(redeem_date) >= $value[0] and day(redeem_date) <= $value[1] and month(redeem_date) = $curr_mon and YEAR(redeem_date) =  YEAR(CURRENT_DATE())");

                $monthForOnSite[] = array_column($weekData, 'total')[0];
                $companyMonth[] = array_column($companyWeekData, 'total')[0];
                $individualMonth[] = array_column($individualWeekData, 'total')[0];
            }
        }
        $allStates = State::get()->toArray();
        $allStatesName = array();

        foreach ($allStates as $value) {
            if ($format == 'Month') {
                // $transactionMonthStateCompany = DB::select("SELECT count(point_used) as total, month(redeem_date) as month FROM customer_reward left join customer on customer_reward.customer_id = customer.id where customer.isIndividual = 0 and customer.state = ? group by month(redeem_date) order by month(redeem_date) asc", array($value['id']));
                // $transactionMonthStateIndividual = DB::select("SELECT count(point_used) as total, month(redeem_date) as month FROM customer_reward left join customer on customer_reward.customer_id = customer.id where customer.isIndividual = 1 and customer.state = ? group by month(redeem_date) order by month(redeem_date) asc", array($value['id']));
                $transactionMonthAll = DB::select("SELECT sum(point_used) as total, month(redeem_date) as month FROM customer_reward left join customer on customer_reward.customer_id = customer.id where customer.state = ? group by month(redeem_date) order by month(redeem_date) asc", array($value['id']));
            } else if ($format == 'Week') {
                $transactionMonthAll = array();
                $redemptionByWeekInCategory = array();

                foreach ($weeks as $value1) {
                    $transactionWeekly = DB::select("SELECT IFNULL(sum(point_used), 0) as total FROM customer_reward left join customer on customer_reward.customer_id = customer.id where day(redeem_date) >= $value1[0] and day(redeem_date) <= $value1[1] and month(redeem_date) = $curr_mon and YEAR(redeem_date) =  YEAR(CURRENT_DATE()) and customer.state = ?", array($value['id']));
                    $transactionWeekly = array_column($transactionWeekly, 'total')[0];
                    array_push($transactionMonthAll, $transactionWeekly);

                    $redemptionByEachWeekInCategory = DB::select("SELECT count(B.id) as total FROM (SELECT customer_reward.id, customer_reward.reward_id from customer_reward where month(redeem_date) = $curr_mon and YEAR(redeem_date) =  YEAR(CURRENT_DATE()) AND day(redeem_date) >= $value1[0] AND day(redeem_date) <= $value1[1]) AS B RIGHT JOIN rewards ON B.reward_id = rewards.id RIGHT JOIN reward_category on rewards.reward_category_id = reward_category.id group by reward_category.id order by reward_category.id");

                    array_push($redemptionByWeekInCategory, array_column($redemptionByEachWeekInCategory, 'total'));
                }
            }
            $dataMonth[] = $transactionMonthAll;

            $allStatesName[] = $value['name'];
        }
        if ($format == 'Month') {

            $transactionMonthAllDataOnly = array_column($monthForOnSite, 'total');
            $data_only = array();

            foreach ($dataMonth as $keyMonth => $month) {
                foreach ($labels as $key => $value) {
                    $tempMonth = array_column($month, 'month');
                    if (!in_array($value, $tempMonth)) {
                        $dataMonth[$keyMonth][] = json_decode(json_encode(array('total' => 0, 'month' => $value)));
                    }
                    usort($dataMonth[$keyMonth], function ($a, $b) {
                        return $a->month <=> $b->month;
                    });
                }
                $temp = array_column($dataMonth[$keyMonth], 'total');
                $data_only[] = $temp;
            }

            // foreach ($dataMonthB2C as $keyMonth => $month) {
            //     foreach ($labels as $key => $value) {
            //         $tempMonth = array_column($month, 'month');
            //         if (!in_array($value, $tempMonth)) {
            //             $dataMonthB2C[$keyMonth][] = json_decode(json_encode(array('total' => 0, 'month' => $value)));
            //         }
            //         usort($dataMonthB2C[$keyMonth], function ($a, $b) {
            //             return $a->month <=> $b->month;
            //         });
            //     }
            //     $temp = array_column($dataMonthB2C[$keyMonth], 'total');
            //     $data_only_B2C[] = $temp;
            // }

            $redemptionByMonthInCategory = array();

            foreach ($labels as $key => $value) {
                if (!in_array($value, $individualMonthLabel)) {
                    array_push($individualMonth, json_decode(json_encode(array('total' => 0, 'month' => $value))));
                }
                if (!in_array($value, $companyMonthLabel)) {
                    array_push($companyMonth, json_decode(json_encode(array('total' => 0, 'month' => $value))));
                }
                $redemptionByEachMonthInCategory = DB::select("SELECT count(B.id) as total FROM (SELECT customer_reward.id, customer_reward.reward_id from customer_reward where month(redeem_date) = $value) AS B RIGHT JOIN rewards ON B.reward_id = rewards.id RIGHT JOIN reward_category on rewards.reward_category_id = reward_category.id group by reward_category.id order by reward_category.id");

                array_push($redemptionByMonthInCategory, array_column($redemptionByEachMonthInCategory, 'total'));

                $dateObj   = DateTime::createFromFormat('!m', $value);
                $monthName = $dateObj->format('M'); // March
                $labels[$key] = $monthName;
            }



            usort($companyMonth, function ($a, $b) {
                return $a->month <=> $b->month;
            });

            usort($individualMonth, function ($a, $b) {
                return $a->month <=> $b->month;
            });

            $companyMonth = array_column($companyMonth, 'total');
            $individualMonth = array_column($individualMonth, 'total');
            $userMonth = array_column($monthForOnSite, 'total');
        }
        $categoryLabel = DB::select("SELECT name from reward_category order by reward_category.id");
        $categoryLabel = array_column($categoryLabel, 'name');

        $colors = Helper::randomColor($allStates);
        $colors_category = Helper::randomColor($categoryLabel);

        if ($format == 'Month') {
            $title = array('Total Redeemed Coins by Users - ' . date('Y'));
            $column = $labels;

            array_unshift($column, ' ');
            $b2b = $companyMonth;
            $b2c = $individualMonth;
            $total = $userMonth;
            array_unshift($b2b, 'B2B');
            array_unshift($b2c, 'B2C');
            array_unshift($total, 'Total');

            $title2 = array('Total Redeemed Coins Across States - ' . date('Y'));
            $column2 = $labels;
            array_unshift($column2, ' ');
            $data2 = $data_only;

            foreach ($allStatesName as $key => $value) {
                array_unshift($data2[$key], $value);
            }

            $title3 = array('Total Number of Redemption by Categories
            - ' . date('Y'));
            $column3 = $categoryLabel;
            array_unshift($column3, ' ');
            $data3 = $redemptionByMonthInCategory;
            foreach ($labels as $key => $value) {
                array_unshift($data3[$key], $value);
            }

            $callback = function () use ($column, $b2b, $b2c, $total, $title, $title2, $column2, $data2, $title3, $column3, $data3) {
                $file = fopen('php://output', 'w');
                fputcsv($file, $title);
                fputcsv($file, $column);
                fputcsv($file, $b2b);
                fputcsv($file, $b2c);
                fputcsv($file, $total);
                fputcsv($file, array(' '));
                fputcsv($file, $title2);
                fputcsv($file, $column2);
                foreach ($data2 as $key => $value) {
                    fputcsv($file, $value);
                }
                fputcsv($file, array(' '));
                fputcsv($file, $title3);
                fputcsv($file, $column3);
                foreach ($data3 as $key => $value) {
                    fputcsv($file, $value);
                }
                fclose($file);
            };
        }

        if ($format == 'Week') {
            $title = array('Total Redeemed Coins by Users - ' . date('F Y'));
            $column = $weekLabel;

            array_unshift($column, ' ');
            $b2b = $companyMonth;
            $b2c = $individualMonth;
            $total = $monthForOnSite;
            array_unshift($b2b, 'B2B');
            array_unshift($b2c, 'B2C');
            array_unshift($total, 'Total');

            $title2 = array('Total Redeemed Coins Across States - ' . date('F Y'));
            $column2 = $weekLabel;
            array_unshift($column2, ' ');
            $data2 = $dataMonth;

            foreach ($allStatesName as $key => $value) {
                array_unshift($data2[$key], $value);
            }

            $title3 = array('Total Number of Redemption by Categories
            - ' . date('F Y'));
            $column3 = $categoryLabel;
            array_unshift($column3, ' ');
            $data3 = $redemptionByWeekInCategory;
            foreach ($weekLabel as $key => $value) {
                array_unshift($data3[$key], $value);
            }

            $callback = function () use ($column, $b2b, $b2c, $total, $title, $title2, $column2, $data2, $title3, $column3, $data3) {
                $file = fopen('php://output', 'w');
                fputcsv($file, $title);
                fputcsv($file, $column);
                fputcsv($file, $b2b);
                fputcsv($file, $b2c);
                fputcsv($file, $total);
                fputcsv($file, array(' '));
                fputcsv($file, $title2);
                fputcsv($file, $column2);
                foreach ($data2 as $key => $value) {
                    fputcsv($file, $value);
                }
                fputcsv($file, array(' '));
                fputcsv($file, $title3);
                fputcsv($file, $column3);
                foreach ($data3 as $key => $value) {
                    fputcsv($file, $value);
                }
                fclose($file);
            };
        }
        $filename = 'reward_performance' . time() . '.csv';
        $headers = array(
            "Content-type" => "text/csv",
            "Content-Disposition" => "attachment; filename=$filename",
            "Pragma" => "no-cache",
            "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
            "Expires" => "0"
        );

        return Response::stream($callback, 200, $headers);


        if ($format == 'Month')
            return array($companyMonth, $individualMonth, $userMonth, $labels,  $allStatesName, $data_only, $colors, $transactionMonthAllDataOnly, $categoryLabel, $redemptionByMonthInCategory, $colors_category);
        if ($format == 'Week')
            return array($companyMonth, $individualMonth, $monthForOnSite, $weekLabel, $allStatesName, $dataMonth, $colors, $categoryLabel, $redemptionByWeekInCategory);
    }

    public function get_reward_performance_district_data(Request $request)
    {
        $date_from = $request->date_from;
        $date_to = $request->date_to;
        $curr_mon = date('m');
        $curr_year = date('Y');
        $format = $request->format;
        $allCities = City::where('state_id', $request->state_id)->get()->toArray();
        if ($date_from == '' && $date_to == '') {
            if ($format == 'Week') {
                $weeks = Helper::dayInWeek();
                $weekLabel = Helper::weekLabel();
            }
            foreach ($allCities as $value) {
                $transactionMonthAll = array();
                if ($format == 'Month') {
                    $transactionMonthCities = DB::select("SELECT sum(point_used) as total, month(redeem_date) as month FROM customer_reward left join customer on customer_reward.customer_id = customer.id where customer.city = ? and year(redeem_date) = $curr_year group by month(redeem_date) order by month(redeem_date) asc", array($value['id']));
                    $dataMonth[] = $transactionMonthCities;
                }
                if ($format == 'Week') {
                    foreach ($weeks as $value1) {
                        $transactionWeekly = DB::select("SELECT IFNULL(sum(point_used), 0) as total FROM customer_reward left join customer on customer_reward.customer_id = customer.id where day(redeem_date) >= $value1[0] and day(redeem_date) <= $value1[1] and month(redeem_date) = $curr_mon and YEAR(redeem_date) =  YEAR(CURRENT_DATE()) and customer.city = ?", array($value['id']));
                        $transactionWeekly = array_column($transactionWeekly, 'total')[0];
                        array_push($transactionMonthAll, $transactionWeekly);
                    }
                    $dataMonth[] = $transactionMonthAll;
                }
                $allStatesName[] = $value['name'];
            }
            if ($format == 'Month') {
                $transactionMonthAllCities = DB::select("SELECT sum(point_used) as total, month(redeem_date) as month FROM customer_reward left join customer on customer_reward.customer_id = customer.id where customer.state = ? and year(redeem_date) = $curr_year group by month(redeem_date) order by month(redeem_date) asc", array($request->state_id));
                $transactionMonthAllCitiesMonths = array_column($transactionMonthAllCities, 'month');

                $allMonth = Helper::monthInThisYear()[0];
                $data_only = array();
                foreach ($dataMonth as $keyMonth => $month) {
                    foreach ($allMonth as $key => $value) {
                        $tempMonth = array_column($month, 'month');
                        if (!in_array($value, $tempMonth)) {
                            $dataMonth[$keyMonth][] = json_decode(json_encode(array('total' => 0, 'month' => $value)));
                        }
                        usort($dataMonth[$keyMonth], function ($a, $b) {
                            return $a->month <=> $b->month;
                        });
                    }
                    $temp = array_column($dataMonth[$keyMonth], 'total');
                    $data_only[] = $temp;
                }
                foreach ($allMonth as $key => $value) {
                    if (!in_array($value, $transactionMonthAllCitiesMonths)) {
                        array_push($transactionMonthAllCities, json_decode(json_encode(array('total' => 0, 'month' => $value))));
                    }
                    $dateObj   = DateTime::createFromFormat('!m', $value);
                    $monthName = $dateObj->format('M'); // March
                    $labels[$key] = $monthName;
                }

                usort($transactionMonthAllCities, function ($a, $b) {
                    return $a->month <=> $b->month;
                });

                $transactionMonthAllCitiesDataOnly = array_column($transactionMonthAllCities, 'total');
            }

            if ($format == 'Week') {
                $transactionMonthAllCitiesDataOnly = array();
                foreach ($weeks as $value1) {
                    $transactionTotalWeek = DB::select("SELECT IFNULL(sum(point_used), 0) as total FROM customer_reward left join customer on customer_reward.customer_id = customer.id where day(redeem_date) >= $value1[0] and day(redeem_date) <= $value1[1] and month(redeem_date) = $curr_mon and YEAR(redeem_date) =  YEAR(CURRENT_DATE()) and customer.state = ?", array($request->state_id));
                    $transactionTotalWeek = array_column($transactionTotalWeek, 'total')[0];
                    array_push($transactionMonthAllCitiesDataOnly, $transactionTotalWeek);
                }
            }
        } else {
            $start    = (new DateTime($date_from))->modify('first day of this month');
            $end      = (new DateTime($date_to))->modify('first day of next month');
            $interval = DateInterval::createFromDateString('1 month');
            $period   = new DatePeriod($start, $interval, $end);
            $dayOfStartDate = date('d', strtotime($date_from));
            $dayOfEndDate = date('d', strtotime($date_to));
            $monthOfEndDate = date('m', strtotime($date_to));

            if ($format == 'Week') {
                $weeks = Helper::dayInWeek();
                $weekLabel = Helper::weekLabel();
            }
            if ($format == 'Month') {
                $labels = array();
                $data_only = array();
                foreach ($allCities as $index => $value) {
                    $cityData = array();
                    foreach ($period as $key => $dt) {
                        $year = $dt->format('Y');
                        $month = $dt->format('m');

                        if ($index == 0) {
                            array_push($labels, $dt->format('M y'));
                            $transactionMonthAllCities = DB::select("SELECT IFNULL(sum(point_used),0) as total FROM customer_reward left join customer on customer_reward.customer_id = customer.id where customer.state = ? and year(redeem_date) = $year and month(redeem_date) = $month and redeem_date >= '$date_from' and redeem_date <= '$date_to'", array($request->state_id));
                            $transactionMonthAllCitiesDataOnly[] = array_column($transactionMonthAllCities, 'total')[0];
                        }

                        $transactionMonthAll = array();
                        $transactionMonthCities = DB::select("SELECT IFNULL(sum(point_used),0) as total FROM customer_reward left join customer on customer_reward.customer_id = customer.id where customer.city = ? and month(redeem_date) = $month and year(redeem_date) = $year and redeem_date >= '$date_from' and redeem_date <= '$date_to'", array($value['id']));
                        $cityData[] = array_column($transactionMonthCities, 'total')[0];
                    }


                    $allStatesName[] = $value['name'];
                    $data_only[] = $cityData;
                }
            }

            if ($format == 'Week') {
                $transactionMonthAllCitiesDataOnly = array();
                $weekLabel = array();
                foreach ($allCities as $index => $value) {
                    $transactionMonthAll = array();
                    foreach ($period as $key => $dt) {
                        $break = false;
                        $curr_year = $dt->format('Y');
                        $curr_mon = $dt->format('m');
                        $weeksLabel = Helper::weekLabel();
                        foreach ($weeks as $key1 => $value1) {
                            if ($break == true)
                                break;
                            if ($key == 0) {
                                if ($dayOfStartDate >= $value1[0] && $dayOfStartDate <= $value1[1] == false) {
                                    continue;
                                }
                            }
                            if ($monthOfEndDate == $curr_mon) {
                                if ($dayOfEndDate >= $value1[0] && $dayOfEndDate <= $value1[1]) {
                                    $break = true;
                                }
                            }
                            if ($index == 0) {
                                array_push($weekLabel, $dt->format('Y M ') . $weeksLabel[$key1]);
                                $transactionTotalWeek = DB::select("SELECT IFNULL(sum(point_used), 0) as total FROM customer_reward left join customer on customer_reward.customer_id = customer.id where day(redeem_date) >= $value1[0] and day(redeem_date) <= $value1[1] and month(redeem_date) = $curr_mon and YEAR(redeem_date) =  YEAR(CURRENT_DATE()) and customer.state = ?", array($request->state_id));
                                $transactionTotalWeek = array_column($transactionTotalWeek, 'total')[0];
                                array_push($transactionMonthAllCitiesDataOnly, $transactionTotalWeek);
                            }

                            $transactionWeekly = DB::select("SELECT IFNULL(sum(point_used), 0) as total FROM customer_reward left join customer on customer_reward.customer_id = customer.id where day(redeem_date) >= $value1[0] and day(redeem_date) <= $value1[1] and month(redeem_date) = $curr_mon and YEAR(redeem_date) =  YEAR(CURRENT_DATE()) and customer.city = ?", array($value['id']));
                            $transactionWeekly = array_column($transactionWeekly, 'total')[0];
                            array_push($transactionMonthAll, $transactionWeekly);
                        }
                    }
                    $dataMonth[] = $transactionMonthAll;
                    $allStatesName[] = $value['name'];
                }
            }
        }



        $colors = Helper::randomColor($allCities);
        if ($format == 'Month')
            $array = array($allStatesName, $colors, $data_only, $labels, $transactionMonthAllCitiesDataOnly);
        if ($format == 'Week')
            $array = array($allStatesName, $colors, $dataMonth, $weekLabel, $transactionMonthAllCitiesDataOnly);

        return $array;
    }

    public function get_redemption_by_category_state(Request $request)
    {
        $date_from = $request->date_from;
        $date_to = $request->date_to;
        $curr_mon = date('m');
        $state = $request->state_id;
        $format = $request->format;
        $curr_year = date('Y');
        $months = Helper::monthInThisYear();
        if ($date_from == '' && $date_to == '') {
            if ($format == 'Month') {

                $labels = $months[0];

                $redemptionByMonthInCategory = array();

                foreach ($labels as $key => $value) {
                    $redemptionByEachMonthInCategory = DB::select("SELECT count(B.id) as total FROM (SELECT customer_reward.id, customer_reward.reward_id from customer_reward left join customer on customer_reward.customer_id = customer.id where year(redeem_date) = $curr_year and month(redeem_date) = $value and customer.state = $state) AS B RIGHT JOIN rewards ON B.reward_id = rewards.id RIGHT JOIN reward_category on rewards.reward_category_id = reward_category.id group by reward_category.id order by reward_category.id");

                    array_push($redemptionByMonthInCategory, array_column($redemptionByEachMonthInCategory, 'total'));
                }
                $labels = $months[1];
            }

            if ($format == 'Week') {
                $weeks = Helper::dayInWeek();
                $weekLabel = Helper::weekLabel();
                $redemptionByWeekInCategory = array();

                foreach ($weeks as $value1) {

                    $redemptionByEachWeekInCategory = DB::select("SELECT count(B.id) as total FROM (SELECT customer_reward.id, customer_reward.reward_id from customer_reward left join customer on customer_reward.customer_id = customer.id where month(redeem_date) = $curr_mon and YEAR(redeem_date) =  YEAR(CURRENT_DATE()) AND day(redeem_date) >= $value1[0] AND day(redeem_date) <= $value1[1] and customer.state = $state) AS B RIGHT JOIN rewards ON B.reward_id = rewards.id RIGHT JOIN reward_category on rewards.reward_category_id = reward_category.id group by reward_category.id order by reward_category.id");

                    array_push($redemptionByWeekInCategory, array_column($redemptionByEachWeekInCategory, 'total'));
                }
            }
        } else {
            $start    = (new DateTime($date_from))->modify('first day of this month');
            $end      = (new DateTime($date_to))->modify('first day of next month');
            $interval = DateInterval::createFromDateString('1 month');
            $period   = new DatePeriod($start, $interval, $end);
            $dayOfStartDate = date('d', strtotime($date_from));
            $dayOfEndDate = date('d', strtotime($date_to));
            $monthOfEndDate = date('m', strtotime($date_to));

            if ($format == 'Month') {
                $labels = array();
                $redemptionByMonthInCategory = array();

                foreach ($period as $key => $dt) {
                    $year = $dt->format('Y');
                    $month = $dt->format('m');


                    array_push($labels, $dt->format('M y'));

                    $redemptionByEachMonthInCategory = DB::select("SELECT count(B.id) as total FROM (SELECT customer_reward.id, customer_reward.reward_id from customer_reward left join customer on customer_reward.customer_id = customer.id where year(redeem_date) = $year and month(redeem_date) = $month and customer.state = $state) AS B RIGHT JOIN rewards ON B.reward_id = rewards.id RIGHT JOIN reward_category on rewards.reward_category_id = reward_category.id group by reward_category.id order by reward_category.id");

                    array_push($redemptionByMonthInCategory, array_column($redemptionByEachMonthInCategory, 'total'));
                }
            }

            if ($format == 'Week') {
                $weeks = Helper::dayInWeek();
                $weekLabels = Helper::weekLabel();
                $weekLabel = array();
                $redemptionByWeekInCategory = array();

                foreach ($period as $key => $dt) {
                    $break = false;
                    $curr_year = $dt->format('Y');
                    $curr_mon = $dt->format('m');
                    foreach ($weeks as $key1 => $value1) {
                        if ($break == true)
                            break;
                        if ($key == 0) {
                            if ($dayOfStartDate >= $value1[0] && $dayOfStartDate <= $value1[1] == false) {
                                continue;
                            }
                        }
                        if ($monthOfEndDate == $curr_mon) {
                            if ($dayOfEndDate >= $value1[0] && $dayOfEndDate <= $value1[1]) {
                                $break = true;
                            }
                        }
                        array_push($weekLabel, $dt->format('Y M ') . $weekLabels[$key1]);

                        $redemptionByEachWeekInCategory = DB::select("SELECT count(B.id) as total FROM (SELECT customer_reward.id, customer_reward.reward_id from customer_reward left join customer on customer_reward.customer_id = customer.id where month(redeem_date) = $curr_mon and YEAR(redeem_date) =  YEAR(CURRENT_DATE()) AND day(redeem_date) >= $value1[0] AND day(redeem_date) <= $value1[1] and customer.state = $state) AS B RIGHT JOIN rewards ON B.reward_id = rewards.id RIGHT JOIN reward_category on rewards.reward_category_id = reward_category.id group by reward_category.id order by reward_category.id");

                        array_push($redemptionByWeekInCategory, array_column($redemptionByEachWeekInCategory, 'total'));
                    }
                }
            }
        }



        $categoryLabel = DB::select("SELECT name from reward_category order by reward_category.id");
        $categoryLabel = array_column($categoryLabel, 'name');

        $colors = Helper::randomColor($categoryLabel);

        if ($format == 'Month') {
            return array($labels, $redemptionByMonthInCategory, $categoryLabel, $colors);
        } else {
            return array($weekLabel, $redemptionByWeekInCategory, $categoryLabel, $colors);
        }
    }

    public function ads_click()
    {
        return view('report.ads_click');
    }

    public function get_ads_click_data(Request $request)
    {
        $date_from = $request->date_from;
        $date_to = $request->date_to;
        $curr_mon = date('m');
        $format = $request->format;
        $promotions =  DB::select("SELECT id,title from promotion order by id asc");
        $promotion_ids = array_column($promotions, 'id');
        $promotion_titles = array_column($promotions, 'title');
        $colors = Helper::randomColor($promotions);
        $months = Helper::monthInThisYear();

        if ($date_from == '' && $date_to == '') {
            if ($format == 'Month') {
                $year = date('Y');
                $dataTotal = array();
                foreach ($months[0] as $value) {
                    $promotionMonthClick  = DB::select("SELECT count(B.id) as total, p.title as name FROM (SELECT id, promotion_id FROM promotion_clicks where month(created_at) = $value and year(created_at) = $year ) AS B RIGHT JOIN promotion p on p.id = B.promotion_id group by p.id,p.title order by p.id");

                    $dataTotal[] = array_column($promotionMonthClick, 'total');
                }
                $months = $months[1];
            }

            if ($format == "Week") {
                $promotionWeekClick = array();
                $weeks = Helper::dayInWeek();
                $weeksLabel = Helper::weekLabel();

                foreach ($weeks as $value1) {
                    $promotionEachWeekClick = DB::select("SELECT count(B.id) as total,promotion.title FROM (SELECT * from promotion_clicks where month(created_at) = $curr_mon and YEAR(created_at) =  YEAR(CURRENT_DATE()) AND day(created_at) >= $value1[0] AND day(created_at) <= $value1[1]) AS B RIGHT JOIN promotion on B.promotion_id = promotion.id group by promotion.id,promotion.title order by promotion.id asc");

                    array_push($promotionWeekClick, array_column($promotionEachWeekClick, 'total'));
                }
            }
        } else {
            $start    = (new DateTime($date_from))->modify('first day of this month');
            $end      = (new DateTime($date_to))->modify('first day of next month');
            $interval = DateInterval::createFromDateString('1 month');
            $period   = new DatePeriod($start, $interval, $end);
            $dayOfStartDate = date('d', strtotime($date_from));
            $dayOfEndDate = date('d', strtotime($date_to));
            $monthOfEndDate = date('m', strtotime($date_to));

            if ($format == 'Month') {
                $dataTotal = array();
                $months = array();
                foreach ($period as $key => $dt) {
                    $year = $dt->format('Y');
                    $month = $dt->format('m');
                    array_push($months, $dt->format('M y'));

                    $promotionMonthClick  = DB::select("SELECT count(B.id) as total, p.title as name FROM (SELECT id, promotion_id FROM promotion_clicks where created_at >= '$date_from' and created_at <= '$date_to' and month(created_at) = $month and year(created_at) = $year ) AS B RIGHT JOIN promotion p on p.id = B.promotion_id group by p.id,p.title order by p.id");

                    $dataTotal[] = array_column($promotionMonthClick, 'total');
                }
            }
            if ($format == "Week") {
                $promotionWeekClick = array();
                $weeks = Helper::dayInWeek();
                $weekLabel = Helper::weekLabel();
                $weeksLabel = array();

                foreach ($period as $key => $dt) {
                    $break = false;
                    $curr_year = $dt->format('Y');
                    $curr_mon = $dt->format('m');
                    foreach ($weeks as $key1 => $value) {
                        if ($break == true)
                            break;
                        if ($key == 0) {
                            if ($dayOfStartDate >= $value[0] && $dayOfStartDate <= $value[1] == false) {
                                continue;
                            }
                        }
                        if ($monthOfEndDate == $curr_mon) {
                            if ($dayOfEndDate >= $value[0] && $dayOfEndDate <= $value[1]) {
                                $break = true;
                            }
                        }
                        array_push($weeksLabel, $dt->format('Y M ') . $weekLabel[$key1]);

                        $promotionEachWeekClick = DB::select("SELECT count(B.id) as total,promotion.title FROM (SELECT * from promotion_clicks where month(created_at) = $curr_mon and YEAR(created_at) =  $curr_year AND day(created_at) >= $value[0] AND day(created_at) <= $value[1]) AS B RIGHT JOIN promotion on B.promotion_id = promotion.id group by promotion.id,promotion.title order by promotion.id asc");

                        array_push($promotionWeekClick, array_column($promotionEachWeekClick, 'total'));
                    }
                }
            }
        }

        if ($format == 'Month')
            return array($months, $dataTotal, $colors, $promotion_ids, $promotion_titles);
        if ($format == 'Week')
            return array($promotionWeekClick, $weeksLabel, $promotion_ids, $promotion_titles, $colors);
    }

    public function get_ads_click_data_get($format)
    {
        //$date_from = $request->date_from;
        //$date_to = $request->date_to;
        $curr_mon = date('m');
        $promotions =  DB::select("SELECT id,title from promotion order by id asc");
        $promotion_ids = array_column($promotions, 'id');
        $promotion_titles = array_column($promotions, 'title');
        $colors = Helper::randomColor($promotions);

        if ($format == 'Month') {
            $months = DB::select("SELECT month(created_at) as mon from promotion_clicks group by month(created_at) order by mon asc ");
            $months = array_column($months, 'mon');

            $promotionMonthClick  = DB::select("SELECT COALESCE( b.counter, 0) as total, month(pc.created_at) AS mon, p.id AS promotion_id FROM promotion_clicks pc CROSS JOIN promotion p LEFT JOIN ( SELECT COUNT(pc.id) AS counter, month(pc.created_at) AS month_red, pc.promotion_id as promotion_id,p.title AS name FROM promotion_clicks pc INNER JOIN promotion p ON pc.promotion_id = p.id GROUP BY p.title, month_red,pc.promotion_id ) AS b ON b.month_red = month(pc.created_at) AND b.promotion_id = p.id GROUP BY p.title, mon,b.counter,p.id ORDER BY promotion_id ASC, mon ASC");

            foreach ($promotionMonthClick as $a) {
                $dataTotal[$a->promotion_id][] = $a->total;
            }

            foreach ($months as $key => $value) {
                $dateObj   = DateTime::createFromFormat('!m', $value);
                $monthName = $dateObj->format('M'); // March
                $months[$key] = $monthName;
            }
            $filename = 'ads_click_month_' . time() . '.csv';
        }

        if ($format == "Week") {
            $promotionWeekClick = array();
            $weeks = Helper::dayInWeek();
            $weeksLabel = Helper::weekLabel();

            foreach ($weeks as $value1) {
                $promotionEachWeekClick = DB::select("SELECT count(B.id) as total,promotion.title FROM (SELECT * from promotion_clicks where month(created_at) = $curr_mon and YEAR(created_at) =  YEAR(CURRENT_DATE()) AND day(created_at) >= $value1[0] AND day(created_at) <= $value1[1]) AS B RIGHT JOIN promotion on B.promotion_id = promotion.id group by promotion.id,promotion.title order by promotion.id asc");

                array_push($promotionWeekClick, array_column($promotionEachWeekClick, 'total'));
            }
            $filename = 'ads_click_week_' . time() . '.csv';
        }
        $headers = array(
            "Content-type" => "text/csv",
            "Content-Disposition" => "attachment; filename=$filename",
            "Pragma" => "no-cache",
            "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
            "Expires" => "0"
        );
        if ($format == "Month") {
            $title = array('Total User Click For Each Promotions - ' . date('Y'));

            $month_row =  $months;
            array_unshift($month_row, ' ');
            $csvInnerData = $dataTotal;
            foreach ($promotion_titles as $key => $value) {
                array_unshift($csvInnerData[$key + 1], $value);
            }
        }
        if ($format == 'Week') {
            $title = array('Total User Click For Each Promotions - ' . date('F Y'));

            $month_row =  $promotion_titles;

            array_unshift($month_row, ' ');
            $csvInnerData = $promotionWeekClick;
            foreach ($weeksLabel as $key => $value) {
                array_unshift($csvInnerData[$key], $value);
            }
        }

        $callback = function () use ($month_row, $csvInnerData, $title) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $title);
            fputcsv($file, $month_row);

            foreach ($csvInnerData as $data) {
                fputcsv($file, $data);
            }
            fclose($file);
        };
        return Response::stream($callback, 200, $headers);
    }

    public function collectionhub_collected_transaction()
    {
        return view('report.collection_hub.collected_transaction');
    }

    public function collectionhub_collected_transaction_data(Request $request)
    {
        if (in_array(4, Auth::user()->users_roles_id())) {
            $collection_hub_id = (Auth::user()->hub_admin->collection_hub_id);
        } elseif (in_array(5, Auth::user()->users_roles_id())) {
            $collection_hub_id = (Auth::user()->hub_reader->collection_hub_id);
        }
        $format = $request->format;
        $date_from = $request->date_from;
        $date_to = $request->date_to;

        $dayOfStartDate = date('d', strtotime($date_from));
        $dayOfEndDate = date('d', strtotime($date_to));
        $monthOfEndDate = date('m', strtotime($date_to));

        $curr_mon =  date('m');
        $months = array();
        $months_name = array();
        $curr_year = date('Y');
        for ($i = 1; $i <= $curr_mon; $i++) {
            array_push($months, $i);
            array_push($months_name, Helper::monthNumberToName($i));
        }
        $weeks = Helper::dayInWeek();
        $weeks_name = Helper::weekLabel();

        $labels = '';

        $companyByMonth = array();
        $individualByMonth = array();
        $totalByMonth = array();

        $companyByWeek = array();
        $individualByWeek = array();
        $totalByWeek = array();

        if ($format == 'Month') {
            if ($date_from == '' && $date_to == '') {
                foreach ($months as $value) {
                    $companyEachMonth = DB::select("SELECT count(collection.id) as total FROM collection left join collection_hub on collection.collection_hub_id = collection_hub.id left join customer on collection.customer_id = customer.id where collection.collection_hub_id = $collection_hub_id and customer.isIndividual = 0 and month(collection.created_at) = $value and collection.status = 1 and year(collection.created_at) = $curr_year");

                    $individualEachMonth = DB::select("SELECT count(collection.id) as total FROM collection left join collection_hub on collection.collection_hub_id = collection_hub.id left join customer on collection.customer_id = customer.id where collection.collection_hub_id = $collection_hub_id and customer.isIndividual = 1 and month(collection.created_at) = $value and collection.status = 1 and year(collection.created_at) = $curr_year");

                    $totalEachMonth = DB::select("SELECT count(collection.id) as total FROM collection left join collection_hub on collection.collection_hub_id = collection_hub.id left join customer on collection.customer_id = customer.id where collection.collection_hub_id = $collection_hub_id and month(collection.created_at) = $value and collection.status = 1 and year(collection.created_at) = $curr_year");

                    $companyEachMonth = array_column($companyEachMonth, 'total');
                    $individualEachMonth = array_column($individualEachMonth, 'total');
                    $totalEachMonth = array_column($totalEachMonth, 'total');

                    array_push($companyByMonth, $companyEachMonth[0]);
                    array_push($individualByMonth, $individualEachMonth[0]);
                    array_push($totalByMonth, $totalEachMonth[0]);
                }
            } else {
                $months_name = array();
                $start    = (new DateTime($date_from))->modify('first day of this month');
                $end      = (new DateTime($date_to))->modify('first day of next month');
                $interval = DateInterval::createFromDateString('1 month');
                $period   = new DatePeriod($start, $interval, $end);

                foreach ($period as $key => $dt) {
                    $year = $dt->format('Y');
                    $month = $dt->format('m');
                    array_push($months_name, $dt->format('M Y'));

                    $companyEachMonth = DB::select("SELECT count(collection.id) as total FROM collection left join collection_hub on collection.collection_hub_id = collection_hub.id left join customer on collection.customer_id = customer.id where collection.collection_hub_id = $collection_hub_id and customer.isIndividual = 0 and (collection.created_at) >= '$date_from' and (collection.created_at) <= '$date_to' and collection.status = 1 and month(collection.created_at) = $month and year(collection.created_at) = $year ");

                    $individualEachMonth = DB::select("SELECT count(collection.id) as total FROM collection left join collection_hub on collection.collection_hub_id = collection_hub.id left join customer on collection.customer_id = customer.id where collection.collection_hub_id = $collection_hub_id and customer.isIndividual = 1 and (collection.created_at) >= '$date_from' and (collection.created_at) <= '$date_to' and collection.status = 1 and month(collection.created_at) = $month and year(collection.created_at) = $year ");

                    $totalEachMonth = DB::select("SELECT count(collection.id) as total FROM collection left join collection_hub on collection.collection_hub_id = collection_hub.id left join customer on collection.customer_id = customer.id where collection.collection_hub_id = $collection_hub_id and (collection.created_at) >= '$date_from' and (collection.created_at) <= '$date_to' and collection.status = 1 and month(collection.created_at) = $month and year(collection.created_at) = $year ");

                    $companyEachMonth = array_column($companyEachMonth, 'total');
                    $individualEachMonth = array_column($individualEachMonth, 'total');
                    $totalEachMonth = array_column($totalEachMonth, 'total');

                    array_push($companyByMonth, $companyEachMonth[0]);
                    array_push($individualByMonth, $individualEachMonth[0]);
                    array_push($totalByMonth, $totalEachMonth[0]);
                }
            }
        } else if ($format == 'Week') {
            if ($date_from == '' && $date_to == '') {
                foreach ($weeks as $value) {
                    $companyEachWeek = DB::select("SELECT count(collection.id) as total FROM collection left join collection_hub on collection.collection_hub_id = collection_hub.id left join customer on collection.customer_id = customer.id where collection.collection_hub_id = $collection_hub_id and customer.isIndividual = 0 and month(collection.created_at) = $curr_mon and collection.status = 1 and year(collection.created_at) = $curr_year and day(collection.created_at) >= $value[0] and day(collection.created_at) <= $value[1]");

                    $individualEachWeek = DB::select("SELECT count(collection.id) as total FROM collection left join collection_hub on collection.collection_hub_id = collection_hub.id left join customer on collection.customer_id = customer.id where collection.collection_hub_id = $collection_hub_id and customer.isIndividual = 1 and month(collection.created_at) = $curr_mon and collection.status = 1 and year(collection.created_at) = $curr_year and day(collection.created_at) >= $value[0] and day(collection.created_at) <= $value[1]");

                    $totalEachWeek = DB::select("SELECT count(collection.id) as total FROM collection left join collection_hub on collection.collection_hub_id = collection_hub.id left join customer on collection.customer_id = customer.id where collection.collection_hub_id = $collection_hub_id and month(collection.created_at) = $curr_mon and collection.status = 1 and year(collection.created_at) = $curr_year and day(collection.created_at) >= $value[0] and day(collection.created_at) <= $value[1] ");

                    $companyEachWeek = array_column($companyEachWeek, 'total');
                    $individualEachWeek = array_column($individualEachWeek, 'total');
                    $totalEachWeek = array_column($totalEachWeek, 'total');

                    array_push($companyByWeek, $companyEachWeek[0]);
                    array_push($individualByWeek, $individualEachWeek[0]);
                    array_push($totalByWeek, $totalEachWeek[0]);
                }
            } else {
                $weeks_name = array();

                $start    = (new DateTime($date_from))->modify('first day of this month');
                $end      = (new DateTime($date_to))->modify('first day of next month');
                $interval = DateInterval::createFromDateString('1 month');
                $period   = new DatePeriod($start, $interval, $end);

                $weeks = Helper::dayInWeek();
                $weeksName = Helper::weekLabel();


                foreach ($period as $key => $dt) {
                    $break = false;
                    $curr_year = $dt->format('Y');
                    $curr_mon = $dt->format('m');
                    foreach ($weeks as $key1 => $value) {
                        if ($break == true)
                            break;
                        if ($key == 0) {
                            if ($dayOfStartDate >= $value[0] && $dayOfStartDate <= $value[1] == false) {
                                continue;
                            }
                        }
                        if ($monthOfEndDate == $curr_mon) {
                            if ($dayOfEndDate >= $value[0] && $dayOfEndDate <= $value[1]) {
                                $break = true;
                            }
                        }
                        array_push($weeks_name, $dt->format('Y M ') . $weeksName[$key1]);

                        $companyEachWeek = DB::select("SELECT count(collection.id) as total FROM collection left join collection_hub on collection.collection_hub_id = collection_hub.id left join customer on collection.customer_id = customer.id where collection.collection_hub_id = $collection_hub_id and customer.isIndividual = 0 and month(collection.created_at) = $curr_mon and collection.status = 1 and year(collection.created_at) = $curr_year and day(collection.created_at) >= $value[0] and day(collection.created_at) <= $value[1]");

                        $individualEachWeek = DB::select("SELECT count(collection.id) as total FROM collection left join collection_hub on collection.collection_hub_id = collection_hub.id left join customer on collection.customer_id = customer.id where collection.collection_hub_id = $collection_hub_id and customer.isIndividual = 1 and month(collection.created_at) = $curr_mon and collection.status = 1 and year(collection.created_at) = $curr_year and day(collection.created_at) >= $value[0] and day(collection.created_at) <= $value[1]");

                        $totalEachWeek = DB::select("SELECT count(collection.id) as total FROM collection left join collection_hub on collection.collection_hub_id = collection_hub.id left join customer on collection.customer_id = customer.id where collection.collection_hub_id = $collection_hub_id and month(collection.created_at) = $curr_mon and collection.status = 1 and year(collection.created_at) = $curr_year and day(collection.created_at) >= $value[0] and day(collection.created_at) <= $value[1] ");

                        $companyEachWeek = array_column($companyEachWeek, 'total');
                        $individualEachWeek = array_column($individualEachWeek, 'total');
                        $totalEachWeek = array_column($totalEachWeek, 'total');

                        array_push($companyByWeek, $companyEachWeek[0]);
                        array_push($individualByWeek, $individualEachWeek[0]);
                        array_push($totalByWeek, $totalEachWeek[0]);
                    }
                }
            }
        }
        if ($format == 'Month') {
            return array($months_name, $companyByMonth, $individualByMonth, $totalByMonth);
        }

        if ($format == 'Week') {
            return array($weeks_name, $companyByWeek, $individualByWeek, $totalByWeek);
        }
    }


    public function collectionhub_collected_waste()
    {
        return view('report.collection_hub.collected_waste');
    }

    public function collectionhub_collected_waste_data(Request $request)
    {
        if (in_array(4, Auth::user()->users_roles_id())) {
            $collection_hub_id = (Auth::user()->hub_admin->collection_hub_id);
        } elseif (in_array(5, Auth::user()->users_roles_id())) {
            $collection_hub_id = (Auth::user()->hub_reader->collection_hub_id);
        }

        $format = $request->format;
        $date_from = $request->date_from;
        $date_to = $request->date_to;

        $categoryLabel = RecycleCategory::get()->toArray();
        $categoryColor = Helper::randomColor($categoryLabel);
        $dayOfStartDate = date('d', strtotime($date_from));
        $dayOfEndDate = date('d', strtotime($date_to));
        $monthOfEndDate = date('m', strtotime($date_to));

        $curr_mon = date('m');
        $months = array();
        $months_name = array();
        $curr_year = date('Y');
        for ($i = 1; $i <= $curr_mon; $i++) {
            array_push($months, $i);
            array_push($months_name, Helper::monthNumberToName($i));
        }
        $weeks = Helper::dayInWeek();
        $weeks_name = Helper::weekLabel();

        $labels = '';

        $categoryByMonth = array();

        $categoryByWeek = array();

        if ($format == 'Month') {
            if ($date_from == '' && $date_to == '') {
                foreach ($months as $value) {
                    $categoryEachMonth = DB::select("SELECT round(ifnull(sum(B.weight),0),2) as total FROM (SELECT cd.weight,rt.recycle_category_id from collection c left join collection_detail cd on c.id = cd.collection_id left join recycle_type rt on cd.recycling_type_id = rt.id where month(c.created_at) = $value and c.status = 1 and year(c.created_at) = $curr_year and c.collection_hub_id = $collection_hub_id) AS B RIGHT JOIN recycle_category rc on rc.id = B.recycle_category_id group by rc.id");

                    $categoryEachMonth = array_column($categoryEachMonth, 'total');

                    array_push($categoryByMonth, $categoryEachMonth);
                }
            } else {
                $months_name = array();
                $start    = (new DateTime($date_from))->modify('first day of this month');
                $end      = (new DateTime($date_to))->modify('first day of next month');
                $interval = DateInterval::createFromDateString('1 month');
                $period   = new DatePeriod($start, $interval, $end);

                foreach ($period as $key => $dt) {
                    $year = $dt->format('Y');
                    $month = $dt->format('m');
                    array_push($months_name, $dt->format('M y'));

                    $categoryEachMonth = DB::select("SELECT round(ifnull(sum(B.weight),0),2) as total FROM (SELECT cd.weight,rt.recycle_category_id from collection c left join collection_detail cd on c.id = cd.collection_id left join recycle_type rt on cd.recycling_type_id = rt.id where c.collection_hub_id = $collection_hub_id and c.status = 1 and c.created_at >= '$date_from' and c.created_at <= '$date_to' and month(c.created_at) = $month and year(c.created_at) = $year) AS B RIGHT JOIN recycle_category rc on rc.id = B.recycle_category_id group by rc.id");

                    $categoryEachMonth = array_column($categoryEachMonth, 'total');

                    array_push($categoryByMonth, $categoryEachMonth);
                }
            }
        } else if ($format == 'Week') {
            if ($date_from == '' && $date_to == '') {
                foreach ($weeks as $value) {
                    $categoryEachWeek = DB::select("SELECT round(ifnull(sum(B.weight),0),2) as total FROM (SELECT cd.weight,rt.recycle_category_id from collection c left join collection_detail cd on c.id = cd.collection_id left join recycle_type rt on cd.recycling_type_id = rt.id where c.collection_hub_id = $collection_hub_id and c.status = 1 and month(c.created_at) = $curr_mon and year(c.created_at) = $curr_year and day(c.created_at) >= $value[0] and day(c.created_at) <= $value[1]) AS B RIGHT JOIN recycle_category rc on rc.id = B.recycle_category_id group by rc.id");

                    $categoryEachWeek = array_column($categoryEachWeek, 'total');

                    array_push($categoryByWeek, $categoryEachWeek);
                }
            } else {
                $weeks_name = array();

                $start    = (new DateTime($date_from))->modify('first day of this month');
                $end      = (new DateTime($date_to))->modify('first day of next month');
                $interval = DateInterval::createFromDateString('1 month');
                $period   = new DatePeriod($start, $interval, $end);

                $weeks = Helper::dayInWeek();
                $weeksName = Helper::weekLabel();

                foreach ($period as $key => $dt) {
                    $break = false;
                    $curr_year = $dt->format('Y');
                    $curr_mon = $dt->format('m');
                    foreach ($weeks as $key1 => $value) {
                        if ($break == true)
                            break;
                        if ($key == 0) {
                            if ($dayOfStartDate >= $value[0] && $dayOfStartDate <= $value[1] == false) {
                                continue;
                            }
                        }
                        if ($monthOfEndDate == $curr_mon) {
                            if ($dayOfEndDate >= $value[0] && $dayOfEndDate <= $value[1]) {
                                $break = true;
                            }
                        }
                        array_push($weeks_name, $dt->format('Y M ') . $weeksName[$key1]);

                        $categoryEachWeek = DB::select("SELECT round(ifnull(sum(B.weight),0),2) as total FROM (SELECT cd.weight,rt.recycle_category_id from collection c left join collection_detail cd on c.id = cd.collection_id left join recycle_type rt on cd.recycling_type_id = rt.id where c.collection_hub_id = $collection_hub_id and month(c.created_at) = $curr_mon and c.status = 1 and year(c.created_at) = $curr_year and day(c.created_at) >= $value[0] and day(c.created_at) <= $value[1]) AS B RIGHT JOIN recycle_category rc on rc.id = B.recycle_category_id group by rc.id");


                        $categoryEachWeek = array_column($categoryEachWeek, 'total');

                        array_push($categoryByWeek, $categoryEachWeek);
                    }
                }
            }
        }
        if ($format == 'Month') {
            return array($months_name, $categoryByMonth, $categoryLabel, $categoryColor);
        }

        if ($format == 'Week') {
            return array($weeks_name, $categoryByWeek, $categoryLabel, $categoryColor);
        }
    }

    public function collectionhub_waste_selling()
    {
        return view('report.collection_hub.waste_selling');
    }

    public function collectionhub_waste_selling_data(Request $request)
    {
        if (in_array(4, Auth::user()->users_roles_id())) {
            $collection_hub_id = (Auth::user()->hub_admin->collection_hub_id);
        } elseif (in_array(5, Auth::user()->users_roles_id())) {
            $collection_hub_id = (Auth::user()->hub_reader->collection_hub_id);
        }

        $hub_type = $request->hub_type;
        $curr_mon = date('m');
        $curr_year = date('Y');
        $format = $request->format;
        $months = Helper::monthInThisYear();
        $weeks = Helper::dayInWeek();
        $weeksLabel = Helper::weekLabel();

        $collectedWaste = array();
        $soldWaste = array();
        $collectedWasteTotal = array();
        $soldWasteTotal = array();
        $date_from = $request->date_from;
        $date_to = $request->date_to;
        $labels = array();
        $dayOfStartDate = date('d', strtotime($date_from));
        $dayOfEndDate = date('d', strtotime($date_to));
        $monthOfEndDate = date('m', strtotime($date_to));

        if ($date_from == '' && $date_to == '') {
            if ($format == 'Month') {
                foreach ($months[0] as  $index => $value) {
                    if ($hub_type == '') {
                        $collectedWaste = DB::select("SELECT IFNULL(round(sum(cd.weight),2),0) as total from collection c left join collection_detail cd on c.id = cd.collection_id left join collection_hub chb on chb.id = c.collection_hub_id where month(c.created_at) = $value and year(c.created_at) = $curr_year and c.status = 1 and c.collection_hub_id = $collection_hub_id");

                        $soldWaste = DB::select("SELECT IFNULL(round(sum(wci.weight),2),0) as total from waste_clearance_schedule wcs left join waste_clearance_item wci on wcs.id = wci.waste_clearance_schedule_id left join collection_hub chb on chb.id = wcs.collection_hub_id where month(wcs.collection_time) = $value and year(wcs.collection_time) = $curr_year and wcs.status = 2 and wcs.collection_hub_id = $collection_hub_id");
                    } else {
                        $collectedWaste = DB::select("SELECT IFNULL(round(sum(cd.weight),2),0) as total from collection c left join collection_detail cd on c.id = cd.collection_id left join collection_hub chb on chb.id = c.collection_hub_id where month(c.created_at) = $value and year(c.created_at) = $curr_year  and chb.type = $hub_type and c.status = 1 and c.collection_hub_id = $collection_hub_id");

                        $soldWaste = DB::select("SELECT IFNULL(round(sum(wci.weight),2),0) as total from waste_clearance_schedule wcs left join waste_clearance_item wci on wcs.id = wci.waste_clearance_schedule_id left join collection_hub chb on chb.id = wcs.collection_hub_id where month(wcs.collection_time) = $value and year(wcs.collection_time) = $curr_year and chb.type = $hub_type and wcs.status = 2 and wcs.collection_hub_id = $collection_hub_id");
                    }

                    $collectedWaste = array_column($collectedWaste, 'total')[0];
                    $soldWaste = array_column($soldWaste, 'total')[0];
                    array_push($collectedWasteTotal, $collectedWaste);
                    array_push($soldWasteTotal, $soldWaste);
                }
                $labels = $months[1];
            }

            if ($format == 'Week') {
                foreach ($weeks as $value) {
                    if ($hub_type == '') {
                        $collectedWaste = DB::select("SELECT IFNULL(round(sum(cd.weight),2),0) as total from collection c left join collection_detail cd on c.id = cd.collection_id left join collection_hub chb on chb.id = c.collection_hub_id where month(c.created_at) = $curr_mon and year(c.created_at) = $curr_year and day(c.created_at) >= $value[0] and day(c.created_at) <= $value[1] and c.status = 1 and c.collection_hub_id = $collection_hub_id");

                        $soldWaste = DB::select("SELECT IFNULL(round(sum(wci.weight),2),0) as total from waste_clearance_schedule wcs left join waste_clearance_item wci on wcs.id = wci.waste_clearance_schedule_id left join collection_hub chb on chb.id = wcs.collection_hub_id where month(wcs.collection_time) = $curr_mon and year(wcs.collection_time) = $curr_year and day(wcs.collection_time) >= $value[0] and day(wcs.collection_time) <= $value[1] and wcs.status = 2 and wcs.collection_hub_id = $collection_hub_id");
                    } else {
                        $collectedWaste = DB::select("SELECT IFNULL(round(sum(cd.weight),2),0) as total from collection c left join collection_detail cd on c.id = cd.collection_id left join collection_hub chb on chb.id = c.collection_hub_id where month(c.created_at) = $curr_mon and year(c.created_at) = $curr_year and day(c.created_at) >= $value[0] and day(c.created_at) <= $value[1] and chb.type = $hub_type and c.status = 1 and c.collection_hub_id = $collection_hub_id");

                        $soldWaste = DB::select("SELECT IFNULL(round(sum(wci.weight),2),0) as total from waste_clearance_schedule wcs left join waste_clearance_item wci on wcs.id = wci.waste_clearance_schedule_id left join collection_hub chb on chb.id = wcs.collection_hub_id where month(wcs.collection_time) = $curr_mon and year(wcs.collection_time) = $curr_year and day(wcs.collection_time) >= $value[0] and day(wcs.collection_time) <= $value[1] and chb.type = $hub_type and wcs.status = 2 and wcs.collection_hub_id = $collection_hub_id");
                    }

                    $collectedWaste = array_column($collectedWaste, 'total')[0];
                    $soldWaste = array_column($soldWaste, 'total')[0];
                    array_push($collectedWasteTotal, $collectedWaste);
                    array_push($soldWasteTotal, $soldWaste);
                }
                $labels = $weeksLabel;
            }
        } else {
            $start    = (new DateTime($date_from))->modify('first day of this month');
            $end      = (new DateTime($date_to))->modify('first day of next month');
            $interval = DateInterval::createFromDateString('1 month');
            $period   = new DatePeriod($start, $interval, $end);
            $dayOfStartDate = date('d', strtotime($date_from));
            $dayOfEndDate = date('d', strtotime($date_to));
            $monthOfEndDate = date('m', strtotime($date_to));

            if ($format == 'Month') {
                foreach ($period as $key => $dt) {
                    $year = $dt->format('Y');
                    $month = $dt->format('m');
                    array_push($labels, $dt->format('M Y'));
                    if ($hub_type == '') {
                        $collectedWaste = DB::select("SELECT IFNULL(round(sum(cd.weight),2),0) as total from collection c left join collection_detail cd on c.id = cd.collection_id left join collection_hub chb on chb.id = c.collection_hub_id where c.created_at >= '$date_from' and c.created_at <= '$date_to' and month(c.created_at) = $month and year(c.created_at) = $year and c.status = 1 and c.collection_hub_id = $collection_hub_id");

                        $soldWaste = DB::select("SELECT IFNULL(round(sum(wci.weight),2),0) as total from waste_clearance_schedule wcs left join waste_clearance_item wci on wcs.id = wci.waste_clearance_schedule_id left join collection_hub chb on chb.id = wcs.collection_hub_id where wcs.collection_time >= '$date_from' and wcs.collection_time <= '$date_to' and month(wcs.collection_time) = $month and year(wcs.collection_time) = $year and wcs.status = 2 and wcs.collection_hub_id = $collection_hub_id");
                    } else {
                        $collectedWaste = DB::select("SELECT IFNULL(round(sum(cd.weight),2),0) as total from collection c left join collection_detail cd on c.id = cd.collection_id left join collection_hub chb on chb.id = c.collection_hub_id where c.created_at >= '$date_from' and c.created_at <= '$date_to' and month(c.created_at) = $month and year(c.created_at) = $year  and chb.type = $hub_type and c.status = 1 and c.collection_hub_id = $collection_hub_id");

                        $soldWaste = DB::select("SELECT IFNULL(round(sum(wci.weight),2),0) as total from waste_clearance_schedule wcs left join waste_clearance_item wci on wcs.id = wci.waste_clearance_schedule_id left join collection_hub chb on chb.id = wcs.collection_hub_id where wcs.collection_time >= '$date_from' and wcs.collection_time <= '$date_to' and month(wcs.collection_time) = $month and year(wcs.collection_time) = $year and chb.type = $hub_type and wcs.status = 2 and wcs.collection_hub_id = $collection_hub_id");
                    }

                    $collectedWaste = array_column($collectedWaste, 'total')[0];
                    $soldWaste = array_column($soldWaste, 'total')[0];
                    array_push($collectedWasteTotal, $collectedWaste);
                    array_push($soldWasteTotal, $soldWaste);
                }
            }

            if ($format == 'Week') {
                foreach ($period as $key => $dt) {
                    $break = false;
                    $curr_year = $dt->format('Y');
                    $curr_mon = $dt->format('m');
                    foreach ($weeks as $key1 => $value1) {
                        if ($break == true)
                            break;
                        if ($key == 0) {
                            if ($dayOfStartDate >= $value1[0] && $dayOfStartDate <= $value1[1] == false) {
                                continue;
                            }
                        }
                        if ($monthOfEndDate == $curr_mon) {
                            if ($dayOfEndDate >= $value1[0] && $dayOfEndDate <= $value1[1]) {
                                $break = true;
                            }
                        }
                        array_push($labels, $dt->format('Y M ') . $weeksLabel[$key1]);
                        if ($hub_type == '') {
                            $collectedWaste = DB::select("SELECT IFNULL(round(sum(cd.weight),2),0) as total from collection c left join collection_detail cd on c.id = cd.collection_id left join collection_hub chb on chb.id = c.collection_hub_id where month(c.created_at) = $curr_mon and year(c.created_at) = $curr_year and day(c.created_at) >= $value1[0] and day(c.created_at) <= $value1[1] and c.status = 1 and c.collection_hub_id = $collection_hub_id");

                            $soldWaste = DB::select("SELECT IFNULL(round(sum(wci.weight),2),0) as total from waste_clearance_schedule wcs left join waste_clearance_item wci on wcs.id = wci.waste_clearance_schedule_id left join collection_hub chb on chb.id = wcs.collection_hub_id where month(wcs.collection_time) = $curr_mon and year(wcs.collection_time) = $curr_year and day(wcs.collection_time) >= $value1[0] and day(wcs.collection_time) <= $value1[1] and wcs.status = 2 and wcs.collection_hub_id = $collection_hub_id");
                        } else {
                            $collectedWaste = DB::select("SELECT IFNULL(round(sum(cd.weight),2),0) as total from collection c left join collection_detail cd on c.id = cd.collection_id left join collection_hub chb on chb.id = c.collection_hub_id where month(c.created_at) = $curr_mon and year(c.created_at) = $curr_year and day(c.created_at) >= $value1[0] and day(c.created_at) <= $value1[1] and chb.type = $hub_type and c.status = 1 and c.collection_hub_id = $collection_hub_id");

                            $soldWaste = DB::select("SELECT IFNULL(round(sum(wci.weight),2),0) as total from waste_clearance_schedule wcs left join waste_clearance_item wci on wcs.id = wci.waste_clearance_schedule_id left join collection_hub chb on chb.id = wcs.collection_hub_id where month(wcs.collection_time) = $curr_mon and year(wcs.collection_time) = $curr_year and day(wcs.collection_time) >= $value1[0] and day(wcs.collection_time) <= $value1[1] and chb.type = $hub_type and wcs.status = 2 and wcs.collection_hub_id = $collection_hub_id");
                        }

                        $collectedWaste = array_column($collectedWaste, 'total')[0];
                        $soldWaste = array_column($soldWaste, 'total')[0];
                        array_push($collectedWasteTotal, $collectedWaste);
                        array_push($soldWasteTotal, $soldWaste);
                    }
                }
            }
        }
        $categoryColor = Helper::randomColor(2);
        return array($labels, $collectedWasteTotal, $soldWasteTotal, $categoryColor);
    }

    public function printPartnerReport(Request $request)
    {
        $merchant_id = $request->merchant_id;
        $secret_ki = $request->secret_ki;
        if (!$secret_ki || $secret_ki != '51J3BLkKDKlGOEFRrOhlW4Vt4SzJqNtnVTKoYcPBTCuf0uD3wJyhnN0y4kV2xsR4pn8mAgIo4VDXXtc1GHpwWYka100QDHJ39uq')
            return 'Invalid';
        $merchant = Merchant::find($merchant_id);
        $monthLabels = array();
        $voucherRedeemedMonth = array();
        $voucherRedeemedWeek = array();
        $userRedeemedMonth = array();
        $userRedeemedWeek = array();
        $weeks = Helper::dayInWeek();
        $weekLabel = Helper::weekLabel();
        $yearMonthWeekLabel = array();
        $userRedeemedWeekLastMonth = array();
        $userReachMonth = array();
        $userClickMonth = array();

        for ($i = 3; $i > 0; $i--) {
            $decrement = '-' . $i . ' Months';
            $month = date("m", strtotime($decrement));
            $month_string = date("M", strtotime($decrement));
            $year = date("Y", strtotime($decrement));
            array_push($monthLabels, $year . '-' . $month_string);

            $voucherRedeemedEachMonth = DB::SELECT("SELECT * FROM customer_reward cr LEFT JOIN rewards r ON cr.reward_id = r.id WHERE r.merchant_id = $merchant_id AND year(redeem_date) = $year AND month(redeem_date) = $month");
            array_push($voucherRedeemedMonth, count($voucherRedeemedEachMonth));

            $userRedeemedEachMonth = DB::SELECT("SELECT DISTINCT(cr.customer_id) FROM customer_reward cr LEFT JOIN rewards r ON cr.reward_id = r.id WHERE r.merchant_id = $merchant_id AND year(redeem_date) = $year AND month(redeem_date) = $month");

            array_push($userRedeemedMonth, count($userRedeemedEachMonth));

            foreach ($weeks as $key => $value) {
                array_push($yearMonthWeekLabel, $year . ' ' . $month_string . ' ' . $weekLabel[$key]);
                $voucherRedeemedEachWeek = DB::SELECT("SELECT * FROM customer_reward cr LEFT JOIN rewards r ON cr.reward_id = r.id WHERE r.merchant_id = $merchant_id AND year(redeem_date) = $year AND month(redeem_date) = $month AND day(redeem_date) >= $value[0] AND day(redeem_date) <= $value[1]");
                array_push($voucherRedeemedWeek, count($voucherRedeemedEachWeek));

                $userRedeemedEachWeek = DB::SELECT("SELECT DISTINCT(cr.customer_id) FROM customer_reward cr LEFT JOIN rewards r ON cr.reward_id = r.id WHERE r.merchant_id = $merchant_id AND year(redeem_date) = $year AND month(redeem_date) = $month AND day(redeem_date) >= $value[0] AND day(redeem_date) <= $value[1]");
                array_push($userRedeemedWeek, count($userRedeemedEachWeek));
                if ($i == 1) {
                    // array_push($userRedeemedWeekLastMonth, count($userRedeemedEachWeek));
                    // $userReach = DB::select("SELECT ifnull(count(id),0) as total FROM login_activity WHERE year(created_at) = $year AND month(created_at) = $month AND day(created_at) >= $value[0] AND day(created_at) <= $value[1]");
                    // array_push($userReachMonth, array_column($userReach, 'total')[0]);

                    // $userClick = DB::select("SELECT ifnull(count(pc.id),0) as total FROM promotion_clicks pc LEFT JOIN promotion p ON pc.promotion_id = p.id WHERE p.merchant_id = $merchant_id AND year(pc.created_at) = $year AND month(pc.created_at) = $month AND day(pc.created_at) >= $value[0] AND day(pc.created_at) <= $value[1]");
                    // array_push($userClickMonth, array_column($userClick, 'total')[0]);
                }
            }

            if ($i == 1) {
                $twoDaysDifferentInMonth = Helper::twoDaysDifferentInMonth();
                $numberOfRedemptionIn2days = array();
                $userReachIn2days = array();
                $userClickIn2days = array();
                foreach ($twoDaysDifferentInMonth[1] as $key => $value) {
                    $numberOfRedemptionSql = DB::select("SELECT ifnull(count(cr.id),0) as total FROM customer_reward cr LEFT JOIN rewards r ON cr.reward_id = r.id WHERE r.merchant_id = $merchant_id AND year(redeem_date) = $year AND month(redeem_date) = $month AND day(redeem_date) >= $value[0] AND day(redeem_date) <= $value[1]");
                    $numberOfRedemptionIn2days[] = array_column($numberOfRedemptionSql, 'total')[0];

                    $userReach = DB::select("SELECT ifnull(count(id),0) as total FROM login_activity WHERE year(created_at) = $year AND month(created_at) = $month AND day(created_at) >= $value[0] AND day(created_at) <= $value[1]");
                    array_push($userReachIn2days, array_column($userReach, 'total')[0]);

                    $userClick = DB::select("SELECT ifnull(count(pc.id),0) as total FROM promotion_clicks pc LEFT JOIN promotion p ON pc.promotion_id = p.id WHERE p.merchant_id = $merchant_id AND year(pc.created_at) = $year AND month(pc.created_at) = $month AND day(pc.created_at) >= $value[0] AND day(pc.created_at) <= $value[1]");
                    array_push($userClickIn2days, array_column($userClick, 'total')[0]);
                }
            }
        }
        // $topUser = DB::SELECT("SELECT c.name, count(cr.id) as total FROM customer_reward cr LEFT JOIN rewards r ON cr.reward_id = r.id LEFT JOIN customer c ON cr.customer_id = c.id WHERE r.merchant_id = $merchant_id GROUP BY cr.customer_id,name ORDER BY total DESC LIMIT 3");
        $topStates = DB::SELECT("SELECT s.name, count(cr.id) as total FROM customer_reward cr LEFT JOIN rewards r ON cr.reward_id = r.id LEFT JOIN customer c ON cr.customer_id = c.id LEFT JOIN state s ON c.state = s.id WHERE r.merchant_id = $merchant_id GROUP BY s.id,s.name ORDER BY total DESC LIMIT 3");
        $topDistricts = DB::SELECT("SELECT concat(ct.name,', ',s.name) as name, count(cr.id) as total FROM customer_reward cr LEFT JOIN rewards r ON cr.reward_id = r.id LEFT JOIN customer c ON cr.customer_id = c.id LEFT JOIN city ct ON c.city = ct.id LEFT JOIN state s ON ct.state_id = s.id WHERE r.merchant_id = $merchant_id GROUP BY ct.id,ct.name,s.name ORDER BY total DESC LIMIT 3");

        $voucherRedeemedWeek = array_chunk($voucherRedeemedWeek, 4);
        $userRedeemedWeek = array_chunk($userRedeemedWeek, 4);
        $yearMonthWeekLabel = array_chunk($yearMonthWeekLabel, 4);
        $colors = Helper::randomColor($yearMonthWeekLabel);

        $forteenDaysLabel = array();
        $userReachForteenDays = array();
        $userClickForteenDays = array();
        $sevenDaysLabel = array();
        $userReachSevenDays = array();
        $userClickSevenDays = array();

        for ($i = 14; $i > 0; $i--) {
            $decrement = '-' . $i . ' Days';
            $month = date("m", strtotime($decrement));
            $month_string = date("M", strtotime($decrement));
            $year = date("Y", strtotime($decrement));
            $day = date("d", strtotime($decrement));
            array_push($forteenDaysLabel, $year . '-' . $month_string . '-' . $day);
            $userReach = DB::select("SELECT ifnull(count(id),0) as total FROM login_activity WHERE year(created_at) = $year AND month(created_at) = $month AND day(created_at) = $day");
            $userClick = DB::select("SELECT ifnull(count(pc.id),0) as total FROM promotion_clicks pc LEFT JOIN promotion p ON pc.promotion_id = p.id WHERE p.merchant_id = $merchant_id AND year(pc.created_at) = $year AND month(pc.created_at) = $month AND day(pc.created_at) = $day");

            array_push($userReachForteenDays, array_column($userReach, 'total')[0]);
            array_push($userClickForteenDays, array_column($userClick, 'total')[0]);


            if ($i <= 7) {
                array_push($sevenDaysLabel, $year . '-' . $month_string . '-' . $day);
                array_push($userReachSevenDays, array_column($userReach, 'total')[0]);
                array_push($userClickSevenDays, array_column($userClick, 'total')[0]);
            }
        }

        $merchantCategory = DB::select("SELECT DISTINCT(r.reward_category_id),rc.name FROM rewards r left join reward_category rc on r.reward_category_id = rc.id where r.merchant_id = '$merchant_id'");
        $merchantsData = array();
        foreach ($merchantCategory as $key1 => $value1) {
            $reward_category_id = $value1->reward_category_id;
            $merchantHighestRedemption = DB::select("SELECT r.merchant_id, count(cr.id) as total FROM customer_reward cr LEFT JOIN rewards r ON cr.reward_id = r.id where r.reward_category_id = '$reward_category_id' group by r.merchant_id order by total DESC LIMIT 5");
            foreach ($merchantHighestRedemption as $key => $value) {
                $merchants = $value->merchant_id;
                $highestState = DB::select("SELECT s.name, count(cr.id) as total FROM customer_reward cr LEFT JOIN rewards r ON cr.reward_id = r.id LEFT JOIN customer c ON cr.customer_id = c.id LEFT JOIN state s ON c.state = s.id WHERE r.merchant_id = $merchants and r.reward_category_id = '$reward_category_id' GROUP BY s.id,s.name ORDER BY total DESC LIMIT 1 ");
                $merchant_state = Merchant::find($value->merchant_id)->state->name;
                $highestCity = DB::select("SELECT ct.name, count(cr.id) as total FROM customer_reward cr LEFT JOIN rewards r ON cr.reward_id = r.id LEFT JOIN customer c ON cr.customer_id = c.id LEFT JOIN city ct ON c.city = ct.id WHERE r.merchant_id = $merchants and r.reward_category_id = '$reward_category_id' GROUP BY ct.id,ct.name ORDER BY total DESC LIMIT 1 ");
                $merchant_state = Merchant::find($value->merchant_id)->state->name;
                $merchant_city = Merchant::find($value->merchant_id)->city->name;
                // $distinctUser = DB::select("SELECT count(DISTINCT(cr.customer_id)) as total FROM customer_reward cr LEFT JOIN rewards r ON cr.reward_id = r.id LEFT JOIN customer c ON cr.customer_id = c.id LEFT JOIN state s ON c.state = s.id WHERE r.merchant_id = $merchants and r.reward_category_id = '$reward_category_id'");
                // $mostCategories = DB::select("SELECT rc.name, count(cr.id) as total FROM customer_reward cr LEFT JOIN rewards r ON cr.reward_id = r.id LEFT JOIN customer c ON cr.customer_id = c.id LEFT JOIN state s ON c.state = s.id LEFT JOIN reward_category rc ON r.reward_category_id = rc.id WHERE r.merchant_id = $merchants group by rc.id,rc.name order by total desc LIMIT 1 ");
                // $merchantHighestRedemption[$key]->state = array_column($highestState, 'name')[0];
                $merchantHighestRedemption[$key]->merchant_location = $merchant_city . ', ' . $merchant_state;
                $merchantHighestRedemption[$key]->highestUserByState = array_column($highestState, 'name')[0];
                $merchantHighestRedemption[$key]->highestUserByCity = array_column($highestCity, 'name')[0];
                // $merchantHighestRedemption[$key]->distinct_user = array_column($distinctUser, 'total')[0];
                // $merchantHighestRedemption[$key]->categories = array_column($mostCategories, 'name')[0];
            }
            $merchantsData[] = $merchantHighestRedemption;
        }
        $topStatesUserClicks =  DB::select("SELECT s.name,count(pc.id) as total from promotion_clicks pc left join users u on pc.user_id = u.id left join customer c on u.id = c.user_id left join state s on c.state = s.id left join promotion p on p.id = pc.promotion_id where p.merchant_id = $merchant_id group by s.id,s.name order by total desc limit 5");

        $topDistrictsUserClicks =  DB::select("SELECT concat(ct.name,', ',s.name) as name,count(pc.id) as total from promotion_clicks pc left join users u on pc.user_id = u.id left join customer c on u.id = c.user_id left join city ct on c.city = ct.id left join state s on ct.state_id = s.id left join promotion p on p.id = pc.promotion_id where p.merchant_id = $merchant_id group by ct.name,s.id,s.name order by total desc limit 5");

        return view('report.partner_export', compact('monthLabels', 'yearMonthWeekLabel', 'weekLabel', 'voucherRedeemedMonth', 'userRedeemedMonth', 'voucherRedeemedWeek', 'userRedeemedWeek', 'userRedeemedWeekLastMonth', 'colors', 'topStates', 'merchantHighestRedemption', 'forteenDaysLabel', 'userReachForteenDays', 'sevenDaysLabel', 'userReachSevenDays', 'userReachMonth', 'userClickMonth', 'userClickSevenDays', 'userClickForteenDays', 'topStatesUserClicks', 'topDistricts', 'topDistrictsUserClicks', 'merchantsData', 'merchantCategory', 'twoDaysDifferentInMonth', 'numberOfRedemptionIn2days', 'userReachIn2days', 'userClickIn2days', 'merchant'));
    }

    public function nuppurchase_index(Request $request)
    {
        $collections = DB::table('collection_detail')
        ->join('collection_hub_recycle','collection_detail.recycling_type_id','=','collection_hub_recycle.recycle_type_id')
        ->join('recycle_type','collection_detail.recycling_type_id','=','recycle_type.id')
        ->join('recycle_category','recycle_type.recycle_category_id','=','recycle_category.id')
        ->rightJoin('collection','collection.id','=','collection_detail.collection_id')
        ->join('customer','collection.customer_id','=','customer.id')
        ->join('collection_hub','collection.collection_hub_id','=','collection_hub.id')
        ->select('collection_detail.collection_id AS collection_id','collection_detail.weight AS weight','collection_detail.total_point AS total_point','collection_hub_recycle.point AS point','recycle_type.name AS type_name','recycle_category.name AS category_name','collection.status','collection.created_at','customer.membership_id AS customer_id','customer.name','collection_hub.hub_name AS location')
        ->where('collection.status',1)
        ->orderBy('collection.created_at')
        ->get();
        if(session()->has('monthinnum'))
        {
            $request->session()->forget('monthinnum');
        }
        else if(session()->has('month'))
        {
            $request->session()->forget('month');
        }
        else if(session()->has('year'))
        {
            $request->session()->forget('year');
        }
        return view('report.accounting.nuppurchase', compact('collections'));
    }

    public function export_nuppurchase_csv()
    {
        return Excel::download(new NupTableExport,'Nuppurchase.csv');
    }

    public function evoucher_index()
    {
        $evouchers = DB::table('customer_reward')
        ->join('rewards','customer_reward.reward_id','=','rewards.id')
        ->join('reward_category','rewards.reward_category_id','=','reward_category.id')
        ->join('customer','customer_reward.customer_id','=','customer.id')
        ->join('merchant','rewards.merchant_id','=','merchant.id')
        ->select('reward_category.name AS reward_category','rewards.title AS reward_name','customer.membership_id AS user_id','customer.name AS user_name','customer_reward.redeem_date','customer_reward.id AS redemption_id','merchant.name as merchant_name','rewards.point as voucher_value','rewards.created_at AS voucher_date','customer_reward.voucher_id AS voucher_code')
        ->get();
        
        return view('report.accounting.evoucher', compact('evouchers'));
    }

    public function export_evoucher_csv()
    {
        return Excel::download(new EVoucherTableExport,'Evoucher.csv');
    }


    public function epoint_index(Request $request)
    {
        $current = Carbon::now();
        $currentMonth = $current->month;
        $currentYear = $current->year;
        $epoints = DB::table('collection')->join('customer','collection.customer_id','=','customer.id')
                    ->where('collection.status',1)
                    ->select('customer.id as cus_id','customer.membership_id as user_id','customer.name as user_name')
                    ->distinct()
                    ->get();

        foreach($epoints as $e)
        {
            $currentMonthBalance = DB::table('customer_point_transaction')->where('customer_id',$e->cus_id)->whereMonth('created_at',$currentMonth)
            ->whereYear('created_at',$currentYear)->where('status','!=',2)->sum('balance');
            $totalBalance = DB::table('customer_point_transaction')->where('customer_id',$e->cus_id)->where('status','!=',2)->sum('balance');
            if(is_null($currentMonthBalance))
            {
                $currentMonthBalance = 0;
            }

            if(is_null($totalBalance))
            {
                $totalBalance = 0;
            }

            $e->previousBalance = $totalBalance - $currentMonthBalance;

            $monthlyearn = DB::table('collection')
            ->where('customer_id',$e->cus_id)
            ->where('collection.status',1)
            ->whereMonth('created_at',$currentMonth)
            ->whereYear('created_at',$currentYear)
            ->sum('total_point');

            if(is_null($monthlyearn))
            {
                $monthlyearn = 0;
            }

            $e->monthlyearn = $monthlyearn;

            $currentMonthRedeem = DB::table('customer_reward')
            ->where('customer_id',$e->cus_id)
            ->whereMonth('created_at',$currentMonth)
            ->whereYear('created_at',$currentYear)
            ->sum('point_used');

            if(is_null($currentMonthRedeem))
            {
                $currentMonthRedeem = 0;
            }

            $e->currentMonthRedeem = $currentMonthRedeem;
            $e->current = $e->previousBalance + $e->monthlyearn - $e->currentMonthRedeem;
            $e->unitvalue = "RM0.01";
            $totalcurrent = (float)$e->current/100;
            $totalcurrent = round($totalcurrent,2);
            $totalcurrent = number_format($totalcurrent, 2, '.', '');
            $e->totalvalue = $totalcurrent;
            $costredeem = (float)$e->currentMonthRedeem/100;
            $costredeem = round($costredeem,2);
            $costredeem = number_format($costredeem, 2, '.', '');
            $e->costredeem = $costredeem; 
        }
        if(session()->has('monthinnum'))
        {
            $request->session()->forget('monthinnum');
        }
        else if(session()->has('month'))
        {
            $request->session()->forget('month');
        }
        else if(session()->has('year'))
        {
            $request->session()->forget('year');
        }
        return view('report.accounting.epoint',compact('epoints'));
    }
    
    public function export_epoint_csv()
    {
        return Excel::download(new EPointTableExport,'Epoint.csv');
    }

    public function nuppurchase_filter(Request $request)
    {
        if(!empty($request->input('month'))) {
            $monthinnum = date('m',strtotime($request->input('month')));
            $month = date('M',strtotime($request->input('month')));
            $year = date('Y',strtotime($request->input('month')));
            $collections = DB::table('collection_detail')
            ->join('collection_hub_recycle','collection_detail.recycling_type_id','=','collection_hub_recycle.recycle_type_id')
            ->join('recycle_type','collection_detail.recycling_type_id','=','recycle_type.id')
            ->join('recycle_category','recycle_type.recycle_category_id','=','recycle_category.id')
            ->rightJoin('collection','collection.id','=','collection_detail.collection_id')
            ->join('customer','collection.customer_id','=','customer.id')
            ->join('collection_hub','collection.collection_hub_id','=','collection_hub.id')
            ->select('collection_detail.collection_id AS collection_id','collection_detail.weight AS weight','collection_detail.total_point AS total_point','collection_hub_recycle.point AS point','recycle_type.name AS type_name','recycle_category.name AS category_name','collection.status','collection.created_at','customer.membership_id AS customer_id','customer.name','collection_hub.hub_name AS location')
            ->where('collection.status',1)
            ->whereMonth('collection.created_at', $monthinnum)
            ->whereYear('collection.created_at', $year)
            ->orderBy('collection.created_at')
            ->get();
            if(session()->has('monthinnum'))
            {
                $request->session()->forget('monthinnum');
            }
            else if(session()->has('month'))
            {
                $request->session()->forget('month');
            }
            else if(session()->has('year'))
            {
                $request->session()->forget('year');
            }
            session(['monthinnum' => $monthinnum]);
            session(['month' => $month]);
            session(['year'=>$year]);
            return view('report.accounting.nuppurchase', compact('collections'));
        } else {
            return redirect()->route('report.accounting.nuppurchase');
        }
    }

    public function epoint_filter(Request $request)
    {
        if(!empty($request->input('month'))) {
            $monthinnum = date('m',strtotime($request->input('month')));
            $month = date('M',strtotime($request->input('month')));
            $year = date('Y',strtotime($request->input('month')));
            $currentMonth = $monthinnum;
            $currentYear = $year;
            $epoints = DB::table('collection')->join('customer','collection.customer_id','=','customer.id')
                        ->where('collection.status',1)
                        ->select('customer.id as cus_id','customer.membership_id as user_id','customer.name as user_name')
                        ->distinct()
                        ->get();
    
            foreach($epoints as $e)
            {
                $currentMonthBalance = DB::table('customer_point_transaction')->where('customer_id',$e->cus_id)->whereMonth('created_at',$currentMonth)
                ->whereYear('created_at',$currentYear)->sum('point');
                $totalBalance = DB::table('customer_point_transaction')->where('customer_id',$e->cus_id)
                ->whereMonth('created_at','<=',$currentMonth)
                ->whereYear('created_at','=',$currentYear)
                ->sum('point');
                $previousTotalBalance = DB::table('customer_point_transaction')->where('customer_id',$e->cus_id)
                ->whereYear('created_at','<',$currentYear)
                ->sum('point');
                $totalBalance = $totalBalance + $previousTotalBalance;
                if(is_null($currentMonthBalance))
                {
                    $currentMonthBalance = 0;
                }
    
                if(is_null($totalBalance))
                {
                    $totalBalance = 0;
                }
    
                $e->previousBalance = $totalBalance - $currentMonthBalance;
    
                $monthlyearn = DB::table('collection')
                ->where('customer_id',$e->cus_id)
                ->whereMonth('created_at',$currentMonth)
                ->whereYear('created_at',$currentYear)
                ->where('collection.status',1)
                ->sum('total_point');
    
                if(is_null($monthlyearn))
                {
                    $monthlyearn = 0;
                }
    
                $e->monthlyearn = $monthlyearn;
    
                $currentMonthRedeem = DB::table('customer_reward')
                ->where('customer_id',$e->cus_id)
                ->whereMonth('created_at',$currentMonth)
                ->whereYear('created_at',$currentYear)
                ->sum('point_used');
    
                if(is_null($currentMonthRedeem))
                {
                    $currentMonthRedeem = 0;
                }
    
                $e->currentMonthRedeem = $currentMonthRedeem;
                $e->current = $e->previousBalance + $e->monthlyearn - $e->currentMonthRedeem;
                $e->unitvalue = "RM0.01";
                $totalcurrent = (float)$e->current/100;
                $totalcurrent = round($totalcurrent,2);
                $totalcurrent = number_format($totalcurrent, 2, '.', '');
                $e->totalvalue = $totalcurrent;
                $costredeem = (float)$e->currentMonthRedeem/100;
                $costredeem = round($costredeem,2);
                $costredeem = number_format($costredeem, 2, '.', '');
                $e->costredeem = $costredeem; 
            }
            
            
            if(session()->has('monthinnum'))
            {
                $request->session()->forget('monthinnum');
            }
            else if(session()->has('month'))
            {
                $request->session()->forget('month');
            }
            else if(session()->has('year'))
            {
                $request->session()->forget('year');
            }
            session(['monthinnum' => $monthinnum]);
            session(['month' => $month]);
            session(['year'=>$year]);
            return view('report.accounting.epoint',compact('epoints'));
        } else {
            return redirect()->route('report.accounting.epoint');
        }
    }

    public function evoucher_filter(Request $request)
    {
        if(!empty($request->input('month')))
        {
            $monthinnum = date('m',strtotime($request->input('month')));
            $month = date('M',strtotime($request->input('month')));
            $year = date('Y',strtotime($request->input('month')));
            $evouchers = DB::table('customer_reward')
            ->join('rewards','customer_reward.reward_id','=','rewards.id')
            ->join('reward_category','rewards.reward_category_id','=','reward_category.id')
            ->join('customer','customer_reward.customer_id','=','customer.id')
            ->join('merchant','rewards.merchant_id','=','merchant.id')
            ->select('reward_category.name AS reward_category','rewards.title AS reward_name','customer.membership_id AS user_id','customer.name AS user_name','customer_reward.redeem_date','customer_reward.id AS redemption_id','merchant.name as merchant_name','rewards.point as voucher_value','rewards.created_at AS voucher_date','customer_reward.voucher_id AS voucher_code')
            ->whereMonth('customer_reward.redeem_date',$monthinnum)
            ->whereYear('customer_reward.redeem_date',$year)
            ->get();
            if(session()->has('monthinnum'))
            {
                $request->session()->forget('monthinnum');
            }
            else if(session()->has('month'))
            {
                $request->session()->forget('month');
            }
            else if(session()->has('year'))
            {
                $request->session()->forget('year');
            }
            session(['monthinnum' => $monthinnum]);
            session(['month' => $month]);
            session(['year'=>$year]);
            return view('report.accounting.evoucher', compact('evouchers'));
        }
        else {
            return redirect()->route('report.accounting.evoucher');
        }
    }

    public function nupsales_index(Request $request)
    {
        $nupsales = DB::table('waste_clearance_schedule')
                    ->join('collection_hub','waste_clearance_schedule.collection_hub_id','=','collection_hub.id')
                    ->leftjoin('waste_clearance_schedule_payment','waste_clearance_schedule.id','=','waste_clearance_schedule_payment.waste_clearance_schedule_id')
                    ->select('waste_clearance_schedule.id as do_num','waste_clearance_schedule.collection_time as do_date','collection_hub.hub_name as hub_location','waste_clearance_schedule.buyer_name','waste_clearance_schedule_payment.id as sales_inv_num','waste_clearance_schedule_payment.invoice_date as sales_inv_date','waste_clearance_schedule_payment.unit_price','waste_clearance_schedule_payment.total_price','waste_clearance_schedule_payment.receipt_date','waste_clearance_schedule_payment.receipt_number','waste_clearance_schedule_payment.total_amount as receipt_amount','waste_clearance_schedule_payment.total_amount as actual_cash_receive')
                    ->get();
        $nupsales_list = [];
        foreach($nupsales as $n)
        {
            $waste_clearance_schedule_item = DB::table('waste_clearance_schedule_item')
                                            ->select('recycle_type_id as type_id','weight')
                                            ->where('waste_clearance_schedule_id',$n->do_num)
                                            ->get();
            
            foreach($waste_clearance_schedule_item as $item)
            {
                $recycle = DB::table('recycle_type')
                        ->join('recycle_category','recycle_type.recycle_category_id','=','recycle_category.id')
                        ->select('recycle_type.name as product_des','recycle_category.name as product_type')
                        ->where('recycle_type.id',$item->type_id)
                        ->first();
                $newitem = (object)[];
                if($n->do_num < 10)
                {
                    $newitem->do_num = "DO-000".$n->do_num;
                }
                else if($n->do_num<100 && $n->do_num > 9)
                {
                    $newitem->do_num = "DO-00".$n->do_num;
                }
                else if($n->do_num<1000 && $n->do_num > 99)
                {
                    $newitem->do_num = "DO-0".$n->do_num;
                }
                else
                {
                    $newitem->do_num = "DO-".$n->do_num;
                }
                $newitem->do_date = $n->do_date;
                $newitem->hub_location = $n->hub_location;
                $newitem->buyer_name = $n->buyer_name;
                $newitem->sales_inv_num = $n->sales_inv_num;
                $newitem->sales_inv_date = $n->sales_inv_date;
                $newitem->unit_price = 0;
                if($n->unit_price != null)
                {
                    $price = json_decode($n->unit_price);
                    foreach($price as $p=>$v)
                    {
                        if($recycle->product_des == $p)
                        {
                            $newitem->unit_price = $v;
                        }
                    }
                }
                $newitem->total_price = $n->total_price;
                $newitem->receipt_date = $n->receipt_date;
                $newitem->receipt_number = $n->receipt_number;
                $newitem->receipt_amount = $n->receipt_amount;
                $newitem->actual_cash_receive = $n->actual_cash_receive;
                $newitem->product_type = $recycle->product_type;
                $newitem->product_des = $recycle->product_des;
                $newitem->do_qty = $item->weight;
                array_push($nupsales_list,$newitem);

            }      
        }
        $nupsales = $nupsales_list;
        if(session()->has('monthinnum'))
        {
            $request->session()->forget('monthinnum');
        }
        else if(session()->has('month'))
        {
            $request->session()->forget('month');
        }
        else if(session()->has('year'))
        {
            $request->session()->forget('year');
        }
       
        return view('report.accounting.nupsales',compact('nupsales'));
    }

    public function nupsales_filter(Request $request)
    {
        if(!empty($request->input('month')))
        {
            $monthinnum = date('m',strtotime($request->input('month')));
            $month = date('M',strtotime($request->input('month')));
            $year = date('Y',strtotime($request->input('month')));
            $nupsales = DB::table('waste_clearance_schedule')
                        ->join('collection_hub','waste_clearance_schedule.collection_hub_id','=','collection_hub.id')
                        ->leftjoin('waste_clearance_schedule_payment','waste_clearance_schedule.id','=','waste_clearance_schedule_payment.waste_clearance_schedule_id')
                        ->select('waste_clearance_schedule.id as do_num','waste_clearance_schedule.collection_time as do_date','collection_hub.hub_name as hub_location','waste_clearance_schedule.buyer_name','waste_clearance_schedule_payment.id as sales_inv_num','waste_clearance_schedule_payment.invoice_date as sales_inv_date','waste_clearance_schedule_payment.unit_price','waste_clearance_schedule_payment.total_price','waste_clearance_schedule_payment.receipt_date','waste_clearance_schedule_payment.receipt_number','waste_clearance_schedule_payment.total_amount as receipt_amount','waste_clearance_schedule_payment.total_amount as actual_cash_receive')
                        ->whereMonth('waste_clearance_schedule.collection_time',$monthinnum)
                        ->whereYear('waste_clearance_schedule.collection_time',$year)
                        ->get();
            $nupsales_list = [];
            foreach($nupsales as $n)
            {
                $product_type = [];
                $product_des = [];
                $do_qty = [];
                $waste_clearance_schedule_item = DB::table('waste_clearance_schedule_item')
                                                ->leftJoin('waste_clearance_schedule','waste_clearance_schedule_item.waste_clearance_schedule_id','=','waste_clearance_schedule.id')
                                                ->select('recycle_type_id as type_id','weight')
                                                ->where('waste_clearance_schedule_id',$n->do_num)
                                                ->whereMonth('waste_clearance_schedule.collection_time',$monthinnum)
                                                ->whereYear('waste_clearance_schedule.collection_time',$year)
                                                ->get();
                foreach($waste_clearance_schedule_item as $item)
                {
                    $recycle = DB::table('recycle_type')
                            ->join('recycle_category','recycle_type.recycle_category_id','=','recycle_category.id')
                            ->select('recycle_type.name as product_des','recycle_category.name as product_type')
                            ->where('recycle_type.id',$item->type_id)
                            ->first();
                            $newitem = (object)[];
                            if($n->do_num < 10)
                            {
                                $newitem->do_num = "DO-000".$n->do_num;
                            }
                            else if($n->do_num<100 && $n->do_num > 9)
                            {
                                $newitem->do_num = "DO-00".$n->do_num;
                            }
                            else if($n->do_num<1000 && $n->do_num > 99)
                            {
                                $newitem->do_num = "DO-0".$n->do_num;
                            }
                            else
                            {
                                $newitem->do_num = "DO-".$n->do_num;
                            }
                            $newitem->do_date = $n->do_date;
                            $newitem->hub_location = $n->hub_location;
                            $newitem->buyer_name = $n->buyer_name;
                            $newitem->sales_inv_num = $n->sales_inv_num;
                            $newitem->sales_inv_date = $n->sales_inv_date;
                            $newitem->unit_price = 0;
                            if($n->unit_price != null)
                            {
                                $price = json_decode($n->unit_price);
                                foreach($price as $p=>$v)
                                {
                                    if($recycle->product_des == $p)
                                    {
                                        $newitem->unit_price = $v;
                                    }
                                }
                            }
                            $newitem->total_price = $n->total_price;
                            $newitem->receipt_date = $n->receipt_date;
                            $newitem->receipt_number = $n->receipt_number;
                            $newitem->receipt_amount = $n->receipt_amount;
                            $newitem->actual_cash_receive = $n->actual_cash_receive;
                            $newitem->product_type = $recycle->product_type;
                            $newitem->product_des = $recycle->product_des;
                            $newitem->do_qty = $item->weight;
                            array_push($nupsales_list,$newitem);
                }                                
            }

            $nupsales = $nupsales_list;

            if(session()->has('monthinnum'))
            {
                $request->session()->forget('monthinnum');
            }
            else if(session()->has('month'))
            {
                $request->session()->forget('month');
            }
            else if(session()->has('year'))
            {
                $request->session()->forget('year');
            }
            session(['monthinnum' => $monthinnum]);
            session(['month' => $month]);
            session(['year'=>$year]);
            return view('report.accounting.nupsales',compact('nupsales'));
        }
        else
        {
            return redirect()->route('report.accounting.nupsales');
        }
    }

    public function export_nupsales_csv()
    {
        return Excel::download(new NupSalesTableExport,'NupSales.csv');
    }

    public function inventory_index(Request $request)
    {
        $weeks = Helper::dayInWeek();
        $current = Carbon::now();
        $currentMonth = $current->month;
        $currentYear = $current->year;
        
        $previousMonth = 0;
            $previousYear = 0; 

            if($currentMonth == 1)
            {
                $previousMonth = 12;
                $previousYear = $currentYear-1; 
            }
            else
            {
                $previousMonth = $currentMonth-1;
                $previousYear = $currentYear; 
            }
            
           
            $balqty = [];
            $balamt = [];

            $transaction = DB::table('transaction_monthly')->where('month',$previousMonth)->Where('year',$previousYear)->select('balqty','balamt')->get();
            
            if($transaction->isEmpty())
            {
                $balqty = [0,0,0,0,0];
                $balamt = [0,0,0,0,0];
            }

            foreach($transaction as $t)
            {
                $balqty = explode(", ",$t->balqty);
                $balamt = explode(", ",$t->balamt);
            }
            //dd($category2);
            //calculate current
            //dd($previousMonth);
            $collection1 = DB::table('collection_detail')->join('recycle_type','collection_detail.recycling_type_id','=','recycle_type.id')
                        ->join('recycle_category','recycle_type.recycle_category_id','=','recycle_category.id')
                        ->join('collection','collection_detail.collection_id','=','collection.id')
                        ->where('collection.status','=','1')
                        ->whereYear('collection_detail.created_at','<',$currentYear)
                        ->get();

            $collection2 = DB::table('collection_detail')->join('recycle_type','collection_detail.recycling_type_id','=','recycle_type.id')
                        ->join('recycle_category','recycle_type.recycle_category_id','=','recycle_category.id')
                        ->join('collection','collection_detail.collection_id','=','collection.id')
                        ->where('collection.status','=','1')
                        ->whereMonth('collection_detail.created_at','<=',$currentMonth)
                        ->whereYear('collection_detail.created_at','=',$currentYear)
                        ->get();

            $collection1n2 = $collection1->merge($collection2);
        
            $collection3 = DB::table('collection_detail')->join('recycle_type','collection_detail.recycling_type_id','=','recycle_type.id')
                        ->join('recycle_category','recycle_type.recycle_category_id','=','recycle_category.id')
                        ->join('collection','collection_detail.collection_id','=','collection.id')
                        ->where('collection.status','=','1')
                        ->whereMonth('collection_detail.created_at','=',$currentMonth)
                        ->whereYear('collection_detail.created_at','=',$currentYear)
                        ->select(DB::raw("collection_detail.*, recycle_type.*, recycle_category.*, collection.*, collection_detail.total_point as total_point, collection_detail.id as id"))
                        ->get();
            //dd($collection2);
            $category = DB::table('recycle_category')->select('id','name')->get();
            
            foreach($category as $key=>$cate)
            {
                $cate->balqty = $balqty[$key];
                $cate->balamt  = $balamt[$key];
                $cate->purchaseqty = 0;
                $cate->purchaseamt = 0;
            }

        
            if($collection3->isEmpty())
            {
                foreach($category as $cate)
                {
                    $cate->purchaseqty = 0;
                    $cate->purchaseamt = 0;   
                }
            }
            else
            {
                foreach($collection3 as $c)
                {
                    foreach($category as $cate)
                    {
                        if($c->recycle_category_id == $cate->id)
                        {
                        $cate->purchaseqty += $c->weight;
                        $cate->purchaseamt += $c->total_point/100;
                        }
                        if($c->recycle_category_id == $cate->id)
                        {
                        $cate->purchaseqty += 0;
                        $cate->purchaseamt += 0;
                        }
                    }
                }
            }
            foreach($category as $c)
            {
                $adjustment = 0;
                $accumulated = 0;
                foreach ($weeks as $key => $week) {
                    $category_id = $c->id;
                    if($key == 0){
                        $accumulated = $c->balqty;
                    }else{
                        $collectedWaste = DB::select("SELECT IFNULL(round(sum(cd.weight),2),0) as total from collection c left join collection_detail cd on c.id = cd.collection_id left join recycle_type rt on cd.recycling_type_id = rt.id where month(c.created_at) = $currentMonth and c.status = 1 and year(c.created_at) = $currentYear and day(c.created_at) >= $week[0] and day(c.created_at) <= $week[1] and status = 1 and rt.recycle_category_id = $category_id");
                        $accumulated += $collectedWaste[0]->total;
                    }
                    $soldWaste = DB::select("SELECT IFNULL(round(sum(wci.weight),2),0) as total from waste_clearance_schedule wcs left join waste_clearance_item wci on wcs.id = wci.waste_clearance_schedule_id left join recycle_type rt on wci.recycle_type_id = rt.id where month(wcs.collection_time) = $currentMonth and year(wcs.collection_time) = $currentYear and day(wcs.collection_time) >= $week[0] and day(wcs.collection_time) <= $week[1] and wcs.status = 2 and rt.recycle_category_id = $category_id");
                    if($soldWaste[0]->total != 0)
                    {
                        $adjustment += $soldWaste[0]->total - $accumulated;
                    }
                }
                //dd($c->balqty  + $c->purchaseqty);
                $x = (float)$c->balqty + (float)$c->purchaseqty;

                if( $x == 0)
                {
                    $c->avgcost = 0;
                }
                else
                {
                    $c->avgcost = round(( (float)$c->balamt +$c->purchaseamt) / ((float)$c->balqty + $c->purchaseqty),2);
                }
                $current_waste_clearance_item = DB::table('waste_clearance_item')->join('recycle_type','waste_clearance_item.recycle_type_id','=','recycle_type.id')
                ->join('recycle_category','recycle_type.recycle_category_id','=','recycle_category.id')
                ->where('recycle_category.id',$c->id)
                ->whereMonth('waste_clearance_item.created_at','=',$currentMonth)
                ->whereYear('waste_clearance_item.created_at','=',$currentYear)
                ->select(DB::raw("SUM(waste_clearance_item.weight) as weight"))->get();
                $c->salesqty = $current_waste_clearance_item[0]->weight;
                $c->salesamt = round($c->salesqty * $c->avgcost,2);
                $c->adjustqty = $adjustment;
                $c->adjustamt = round($c->adjustqty * $c->avgcost,2);
                $c->currentqty = round((float)$c->balqty + $c->purchaseqty - $c->salesqty + $c->adjustqty,2);
                $c->currentamt = round((float)$c->balamt + $c->purchaseamt - $c->salesamt + $c->adjustamt,2);

            }
        //dd($category);    
        if(session()->has('monthinnum'))
        {
            $request->session()->forget('monthinnum');
        }
        else if(session()->has('month'))
        {
            $request->session()->forget('month');
        }
        else if(session()->has('year'))
        {
            $request->session()->forget('year');
        }
       
        return view('report.accounting.inventory',compact('category'));        
    }
    
    public function inventory_filter(Request $request)
    {
        $weeks = Helper::dayInWeek();
        if(!empty($request->input('month')))
        {
            $monthinnum = date('m',strtotime($request->input('month')));
            $month = date('M',strtotime($request->input('month')));
            $year = date('Y',strtotime($request->input('month')));
            $currentMonth = $monthinnum;
            $currentYear = $year;
            $previousMonth = 0;
            $previousYear = 0; 

            if($currentMonth == 1)
            {
                $previousMonth = 12;
                $previousYear = $year-1; 
            }
            else
            {
                $previousMonth = $monthinnum-1;
                $previousYear = $year; 
            }
            $balqty = [];
            $balamt = [];

            $transaction = DB::table('transaction_monthly')->where('month',$previousMonth)->Where('year',$previousYear)->select('balqty','balamt')->get();
            
            if($transaction->isEmpty())
            {
                $balqty = [0,0,0,0,0];
                $balamt = [0,0,0,0,0];
            }
            foreach($transaction as $t)
            {
                $balqty = explode(", ",$t->balqty);
                $balamt = explode(", ",$t->balamt);
            }
            //dd($category2);
            //calculate current
            //dd($previousMonth);
            $collection1 = DB::table('collection_detail')->join('recycle_type','collection_detail.recycling_type_id','=','recycle_type.id')
                        ->join('recycle_category','recycle_type.recycle_category_id','=','recycle_category.id')
                        ->join('collection','collection_detail.collection_id','=','collection.id')
                        ->where('collection.status','=','1')
                        ->whereYear('collection_detail.created_at','<',$currentYear)
                        ->get();

            $collection2 = DB::table('collection_detail')->join('recycle_type','collection_detail.recycling_type_id','=','recycle_type.id')
                        ->join('recycle_category','recycle_type.recycle_category_id','=','recycle_category.id')
                        ->join('collection','collection_detail.collection_id','=','collection.id')
                        ->where('collection.status','=','1')
                        ->whereMonth('collection_detail.created_at','<=',$currentMonth)
                        ->whereYear('collection_detail.created_at','=',$currentYear)
                        ->get();

            $collection1n2 = $collection1->merge($collection2);
        
            $collection3 = DB::table('collection_detail')->join('recycle_type','collection_detail.recycling_type_id','=','recycle_type.id')
                        ->join('recycle_category','recycle_type.recycle_category_id','=','recycle_category.id')
                        ->join('collection','collection_detail.collection_id','=','collection.id')
                        ->where('collection.status','=','1')
                        ->whereMonth('collection_detail.created_at','=',$currentMonth)
                        ->whereYear('collection_detail.created_at','=',$currentYear)
                        ->select(DB::raw("collection_detail.*, recycle_type.*, recycle_category.*, collection.*, collection_detail.total_point as total_point, collection_detail.id as id"))
                        ->get();
            $prevMonthCollection = DB::table('collection_detail')->join('recycle_type','collection_detail.recycling_type_id','=','recycle_type.id')
                        ->join('recycle_category','recycle_type.recycle_category_id','=','recycle_category.id')
                        ->join('collection','collection_detail.collection_id','=','collection.id')
                        ->where('collection.status','=','1')
                        ->whereMonth('collection_detail.created_at','=',$previousMonth)
                        ->whereYear('collection_detail.created_at','=',$currentYear)
                        ->select(DB::raw("collection_detail.*, recycle_type.*, recycle_category.*, collection.*, collection_detail.total_point as total_point, collection_detail.id as id"))
                        ->get();
            //dd($collection2);
            $category = DB::table('recycle_category')->select('id','name')->get();
            
            foreach($category as $key=>$cate)
            {
                $cate->balqty = $balqty[$key];
                $cate->balamt  = $balamt[$key];
                $cate->purchaseqty = 0;
                $cate->purchaseamt = 0;
            }

        
            if($collection3->isEmpty())
            {
                foreach($category as $cate)
                {
                    $cate->purchaseqty = 0;
                    $cate->purchaseamt = 0;   
                }
            }
            else
            {
                foreach($collection3 as $c)
                {
                    foreach($category as $cate)
                    {
                        if($c->recycle_category_id == $cate->id)
                        {
                        $cate->purchaseqty += $c->weight;
                        $cate->purchaseamt += $c->total_point/100;
                        }
                        if($c->recycle_category_id == $cate->id)
                        {
                        $cate->purchaseqty += 0;
                        $cate->purchaseamt += 0;
                        }
                    }
                }
            }
            foreach($category as $c)
            {
                $adjustment = 0;
                $accumulated = 0;
                foreach ($weeks as $key => $week) {
                    $category_id = $c->id;
                    if($key == 0){
                        $accumulated = $c->balqty;
                    }else{
                        $collectedWaste = DB::select("SELECT IFNULL(round(sum(cd.weight),2),0) as total from collection c left join collection_detail cd on c.id = cd.collection_id left join recycle_type rt on cd.recycling_type_id = rt.id where month(c.created_at) = $currentMonth and c.status = 1 and year(c.created_at) = $currentYear and day(c.created_at) >= $week[0] and day(c.created_at) <= $week[1] and status = 1 and rt.recycle_category_id = $category_id");
                        $accumulated += $collectedWaste[0]->total;
                    }
                    $soldWaste = DB::select("SELECT IFNULL(round(sum(wci.weight),2),0) as total from waste_clearance_schedule wcs left join waste_clearance_item wci on wcs.id = wci.waste_clearance_schedule_id left join recycle_type rt on wci.recycle_type_id = rt.id where month(wcs.collection_time) = $currentMonth and year(wcs.collection_time) = $currentYear and day(wcs.collection_time) >= $week[0] and day(wcs.collection_time) <= $week[1] and wcs.status = 2 and rt.recycle_category_id = $category_id");
                    if($soldWaste[0]->total != 0)
                    {
                        $adjustment += $soldWaste[0]->total - $accumulated;
                    }
                }
                //dd($c->balqty  + $c->purchaseqty);
                $x = (float)$c->balqty + (float)$c->purchaseqty;

                if( $x == 0)
                {
                    $c->avgcost = 0;
                }
                else
                {
                    $c->avgcost = round(( (float)$c->balamt +$c->purchaseamt) / ((float)$c->balqty + $c->purchaseqty),2);
                }
                $current_waste_clearance_item = DB::table('waste_clearance_item')->join('recycle_type','waste_clearance_item.recycle_type_id','=','recycle_type.id')
                ->join('recycle_category','recycle_type.recycle_category_id','=','recycle_category.id')
                ->where('recycle_category.id',$c->id)
                ->whereMonth('waste_clearance_item.created_at','=',$currentMonth)
                ->whereYear('waste_clearance_item.created_at','=',$currentYear)
                ->select(DB::raw("SUM(waste_clearance_item.weight) as weight"))->get();
                $c->salesqty = $current_waste_clearance_item[0]->weight;
                $c->salesamt = round($c->salesqty * $c->avgcost,2);
                $c->adjustqty = $adjustment;
                $c->adjustamt = round($c->adjustqty * $c->avgcost,2);
                $c->currentqty = round((float)$c->balqty + $c->purchaseqty - $c->salesqty + $c->adjustqty,2);
                $c->currentamt = round((float)$c->balamt + $c->purchaseamt - $c->salesamt + $c->adjustamt,2);

            }
        //dd($category);
       // dd($category2);
            if(session()->has('monthinnum'))
            {
                $request->session()->forget('monthinnum');
            }
            else if(session()->has('month'))
            {
                $request->session()->forget('month');
            }
            else if(session()->has('year'))
            {
                $request->session()->forget('year');
            }
            session(['monthinnum' => $monthinnum]);
            session(['month' => $month]);
            session(['year'=>$year]);
            $date = $year.' '.$month;
            return view('report.accounting.inventory',compact('category','date'));
        }
        else
        {
            return redirect()->route('report.accounting.inventory');
        }
    }

    public function export_inventory_csv()
    {
        return Excel::download(new InventoryTableExport,'Inventory.csv');
    }

    public function closingstock_index(Request $request)
    {
        $weeks = Helper::dayInWeek();
        $current = Carbon::now();
        $currentMonth = $current->month;
        $currentYear = $current->year;
        
        $previousMonth = 0;
            $previousYear = 0; 

            if($currentMonth == 1)
            {
                $previousMonth = 12;
                $previousYear = $currentYear-1; 
            }
            else
            {
                $previousMonth = $currentMonth-1;
                $previousYear = $currentYear; 
            }
            
           
            $balqty = [];
            $balamt = [];

            $transaction = DB::table('transaction_monthly')->where('month',$previousMonth)->Where('year',$previousYear)->select('balqty','balamt')->get();
            
            if($transaction->isEmpty())
            {
                $balqty = [0,0,0,0,0];
                $balamt = [0,0,0,0,0];
            }

            foreach($transaction as $t)
            {
                $balqty = explode(", ",$t->balqty);
                $balamt = explode(", ",$t->balamt);
            }
            //dd($category2);
            //calculate current
            //dd($previousMonth);
            $collection1 = DB::table('collection_detail')->join('recycle_type','collection_detail.recycling_type_id','=','recycle_type.id')
                        ->join('recycle_category','recycle_type.recycle_category_id','=','recycle_category.id')
                        ->join('collection','collection_detail.collection_id','=','collection.id')
                        ->where('collection.status','=','1')
                        ->whereYear('collection_detail.created_at','<',$currentYear)
                        ->get();

            $collection2 = DB::table('collection_detail')->join('recycle_type','collection_detail.recycling_type_id','=','recycle_type.id')
                        ->join('recycle_category','recycle_type.recycle_category_id','=','recycle_category.id')
                        ->join('collection','collection_detail.collection_id','=','collection.id')
                        ->where('collection.status','=','1')
                        ->whereMonth('collection_detail.created_at','<=',$currentMonth)
                        ->whereYear('collection_detail.created_at','=',$currentYear)
                        ->get();

            $collection1n2 = $collection1->merge($collection2);
        
            $collection3 = DB::table('collection_detail')->join('recycle_type','collection_detail.recycling_type_id','=','recycle_type.id')
                        ->join('recycle_category','recycle_type.recycle_category_id','=','recycle_category.id')
                        ->join('collection','collection_detail.collection_id','=','collection.id')
                        ->where('collection.status','=','1')
                        ->whereMonth('collection_detail.created_at','=',$currentMonth)
                        ->whereYear('collection_detail.created_at','=',$currentYear)
                        ->get();
            //dd($collection2);
            $category = DB::table('recycle_category')->select('id','name')->get();
            
            foreach($category as $key=>$cate)
            {
                $cate->balqty = $balqty[$key];
                $cate->balamt  = $balamt[$key];
                $cate->purchaseqty = 0;
                $cate->purchaseamt = 0;
            }

        
            if($collection3->isEmpty())
            {
                foreach($category as $cate)
                {
                    $cate->purchaseqty = 0;
                    $cate->purchaseamt = 0;   
                }
            }
            else
            {
                foreach($collection3 as $c)
                {
                    foreach($category as $cate)
                    {
                        if($c->recycle_category_id == $cate->id)
                        {
                        $cate->purchaseqty += $c->weight;
                        $cate->purchaseamt += $c->total_point/100;
                        }
                        if($c->recycle_category_id == $cate->id)
                        {
                        $cate->purchaseqty += 0;
                        $cate->purchaseamt += 0;
                        }
                    }
                }
            }
            foreach($category as $c)
            {
                $adjustment = 0;
                $accumulated = 0;
                foreach ($weeks as $key => $week) {
                    $category_id = $c->id;
                    if($key == 0){
                        $accumulated = $c->balqty;
                    }else{
                        $collectedWaste = DB::select("SELECT IFNULL(round(sum(cd.weight),2),0) as total from collection c left join collection_detail cd on c.id = cd.collection_id left join recycle_type rt on cd.recycling_type_id = rt.id where month(c.created_at) = $currentMonth and c.status = 1 and year(c.created_at) = $currentYear and day(c.created_at) >= $week[0] and day(c.created_at) <= $week[1] and status = 1 and rt.recycle_category_id = $category_id");
                        $accumulated += $collectedWaste[0]->total;
                    }

                    $soldWaste = DB::select("SELECT IFNULL(round(sum(wci.weight),2),0) as total from waste_clearance_schedule wcs left join waste_clearance_item wci on wcs.id = wci.waste_clearance_schedule_id left join recycle_type rt on wci.recycle_type_id = rt.id where month(wcs.collection_time) = $currentMonth and year(wcs.collection_time) = $currentYear and day(wcs.collection_time) >= $week[0] and day(wcs.collection_time) <= $week[1] and wcs.status = 2 and rt.recycle_category_id = $category_id");
                    if($soldWaste[0]->total != 0)
                    {
                        $adjustment += $soldWaste[0]->total - $accumulated;
                    }
                }

                //dd($c->balqty  + $c->purchaseqty);
                $x = (float)$c->balqty + (float)$c->purchaseqty;

                if( $x == 0)
                {
                    $c->avgcost = 0;
                }
                else
                {
                    $c->avgcost = round(( (float)$c->balamt +$c->purchaseamt) / ((float)$c->balqty + $c->purchaseqty),2);
                }
                $current_waste_clearance_item = DB::table('waste_clearance_item')->join('recycle_type','waste_clearance_item.recycle_type_id','=','recycle_type.id')
                ->join('recycle_category','recycle_type.recycle_category_id','=','recycle_category.id')
                ->where('recycle_category.id',$c->id)
                ->whereMonth('waste_clearance_item.created_at','=',$currentMonth)
                ->whereYear('waste_clearance_item.created_at','=',$currentYear)
                ->select(DB::raw("SUM(waste_clearance_item.weight) as weight"))->get();
                $salesqty = $current_waste_clearance_item[0]->weight;
                $c->salesqty = $salesqty;
                $c->salesamt = round($c->salesqty * $c->avgcost,2);
                $c->adjustqty = $adjustment;
                $c->adjustamt = round($c->adjustqty * $c->avgcost,2);
                $c->currentqty = round((float)$c->balqty + $c->purchaseqty - $c->salesqty + $c->adjustqty,2);
                $c->currentamt = round((float)$c->balamt + $c->purchaseamt - $c->salesamt + $c->adjustamt,2);

            }
        //dd($category);    
        if(session()->has('monthinnum'))
        {
            $request->session()->forget('monthinnum');
        }
        else if(session()->has('month'))
        {
            $request->session()->forget('month');
        }
        else if(session()->has('year'))
        {
            $request->session()->forget('year');
        }
       
        return view('report.accounting.closing',compact('category'));    
    }
    
    public function closingstock_filter(Request $request)
    {
        $weeks = Helper::dayInWeek();
        if(!empty($request->input('month')))
        {
            $monthinnum = date('m',strtotime($request->input('month')));
            $month = date('M',strtotime($request->input('month')));
            $year = date('Y',strtotime($request->input('month')));
            $currentMonth = $monthinnum;
            $currentYear = $year;
            $previousMonth = 0;
            $previousYear = 0; 

            if($currentMonth == 1)
            {
                $previousMonth = 12;
                $previousYear = $currentYear-1; 
            }
            else
            {
                $previousMonth = $currentMonth-1;
                $previousYear = $currentYear; 
            }
            
           
            $balqty = [];
            $balamt = [];

            $transaction = DB::table('transaction_monthly')->where('month',$previousMonth)->Where('year',$previousYear)->select('balqty','balamt')->get();
            
            if($transaction->isEmpty())
            {
                $balqty = [0,0,0,0,0];
                $balamt = [0,0,0,0,0];
            }

            foreach($transaction as $t)
            {
                $balqty = explode(", ",$t->balqty);
                $balamt = explode(", ",$t->balamt);
            }
            //dd($category2);
            //calculate current
            //dd($previousMonth);
            $collection1 = DB::table('collection_detail')->join('recycle_type','collection_detail.recycling_type_id','=','recycle_type.id')
                        ->join('recycle_category','recycle_type.recycle_category_id','=','recycle_category.id')
                        ->join('collection','collection_detail.collection_id','=','collection.id')
                        ->where('collection.status','=','1')
                        ->whereYear('collection_detail.created_at','<',$currentYear)
                        ->get();

            $collection2 = DB::table('collection_detail')->join('recycle_type','collection_detail.recycling_type_id','=','recycle_type.id')
                        ->join('recycle_category','recycle_type.recycle_category_id','=','recycle_category.id')
                        ->join('collection','collection_detail.collection_id','=','collection.id')
                        ->where('collection.status','=','1')
                        ->whereMonth('collection_detail.created_at','<=',$currentMonth)
                        ->whereYear('collection_detail.created_at','=',$currentYear)
                        ->get();

            $collection1n2 = $collection1->merge($collection2);
        
            $collection3 = DB::table('collection_detail')->join('recycle_type','collection_detail.recycling_type_id','=','recycle_type.id')
                        ->join('recycle_category','recycle_type.recycle_category_id','=','recycle_category.id')
                        ->join('collection','collection_detail.collection_id','=','collection.id')
                        ->where('collection.status','=','1')
                        ->whereMonth('collection_detail.created_at','=',$currentMonth)
                        ->whereYear('collection_detail.created_at','=',$currentYear)
                        ->get();
            //dd($collection2);
            $category = DB::table('recycle_category')->select('id','name')->get();
            
            foreach($category as $key=>$cate)
            {
                $cate->balqty = $balqty[$key];
                $cate->balamt  = $balamt[$key];
                $cate->purchaseqty = 0;
                $cate->purchaseamt = 0;
            }

        
            if($collection3->isEmpty())
            {
                foreach($category as $cate)
                {
                    $cate->purchaseqty = 0;
                    $cate->purchaseamt = 0;   
                }
            }
            else
            {
                foreach($collection3 as $c)
                {
                    foreach($category as $cate)
                    {
                        if($c->recycle_category_id == $cate->id)
                        {
                        $cate->purchaseqty += $c->weight;
                        $cate->purchaseamt += $c->total_point/100;
                        }
                        if($c->recycle_category_id == $cate->id)
                        {
                        $cate->purchaseqty += 0;
                        $cate->purchaseamt += 0;
                        }
                    }
                }
            }
            foreach($category as $c)
            {
                $adjustment = 0;
                $accumulated = 0;
                foreach ($weeks as $key => $week) {
                    $category_id = $c->id;
                    if($key == 0){
                        $accumulated = $c->balqty;
                    }else{
                        $collectedWaste = DB::select("SELECT IFNULL(round(sum(cd.weight),2),0) as total from collection c left join collection_detail cd on c.id = cd.collection_id left join recycle_type rt on cd.recycling_type_id = rt.id where month(c.created_at) = $currentMonth and c.status = 1 and year(c.created_at) = $currentYear and day(c.created_at) >= $week[0] and day(c.created_at) <= $week[1] and status = 1 and rt.recycle_category_id = $category_id");
                        $accumulated += $collectedWaste[0]->total;
                    }

                    $soldWaste = DB::select("SELECT IFNULL(round(sum(wci.weight),2),0) as total from waste_clearance_schedule wcs left join waste_clearance_item wci on wcs.id = wci.waste_clearance_schedule_id left join recycle_type rt on wci.recycle_type_id = rt.id where month(wcs.collection_time) = $currentMonth and year(wcs.collection_time) = $currentYear and day(wcs.collection_time) >= $week[0] and day(wcs.collection_time) <= $week[1] and wcs.status = 2 and rt.recycle_category_id = $category_id");
                    if($soldWaste[0]->total != 0)
                    {
                        $adjustment += $soldWaste[0]->total - $accumulated;
                    }
                }
                //dd($c->balqty  + $c->purchaseqty);
                $x = (float)$c->balqty + (float)$c->purchaseqty;

                if( $x == 0)
                {
                    $c->avgcost = 0;
                }
                else
                {
                    $c->avgcost = round(( (float)$c->balamt +$c->purchaseamt) / ((float)$c->balqty + $c->purchaseqty),2);
                }
                $current_waste_clearance_item = DB::table('waste_clearance_item')->join('recycle_type','waste_clearance_item.recycle_type_id','=','recycle_type.id')
                ->join('recycle_category','recycle_type.recycle_category_id','=','recycle_category.id')
                ->where('recycle_category.id',$c->id)
                ->whereMonth('waste_clearance_item.created_at','=',$currentMonth)
                ->whereYear('waste_clearance_item.created_at','=',$currentYear)
                ->select(DB::raw("SUM(waste_clearance_item.weight) as weight"))->get();
                $salesqty = $current_waste_clearance_item[0]->weight;
                $c->salesqty = $salesqty;
                $c->salesamt = round($c->salesqty * $c->avgcost,2);
                $c->adjustqty = ((float)$c->balqty + $c->purchaseqty) - $c->salesqty;
                $c->adjustqty = $adjustment;
                $c->adjustamt = round($c->adjustqty * $c->avgcost,2);
                $c->currentqty = round((float)$c->balqty + $c->purchaseqty - $c->salesqty + $c->adjustqty,2);
                $c->currentamt = round((float)$c->balamt + $c->purchaseamt - $c->salesamt + $c->adjustamt,2);

            }

            if(session()->has('monthinnum'))
            {
                $request->session()->forget('monthinnum');
            }
            else if(session()->has('month'))
            {
                $request->session()->forget('month');
            }
            else if(session()->has('year'))
            {
                $request->session()->forget('year');
            }
            session(['monthinnum' => $monthinnum]);
            session(['month' => $month]);
            session(['year'=>$year]);
            $date = $year.' '.$month;
            return view('report.accounting.closing',compact('category','date'));
        }
        else
        {
            return redirect()->route('report.accounting.closingstock');
        }
    }

    public function export_closingstock_csv()
    {
        return Excel::download(new ClosingStockTableExport,'ClosingStocks.csv');
    }
}
