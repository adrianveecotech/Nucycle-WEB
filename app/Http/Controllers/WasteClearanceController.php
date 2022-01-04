<?php

namespace App\Http\Controllers;

use App\Helper\Helper;
use App\Models\CollectionHub;
use App\Models\CollectionHubRecycleType;
use App\Models\RecycleType;
use App\Models\User;
use App\Models\WasteClearanceSchedule;
use App\Models\WasteClearanceScheduleItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class WasteClearanceController extends Controller
{
    public function index()
    {
        $schedules = WasteClearanceSchedule::get();
        return view('waste_clearance.index', compact('schedules'));
    }

    public function view($id)
    {
        $schedule = WasteClearanceSchedule::find($id);
        $items = WasteClearanceScheduleItem::where('waste_clearance_schedule_id', $id)->join('recycle_type', 'recycle_type.id', '=', 'waste_clearance_schedule_item.recycle_type_id')->get(['recycle_type.name', 'waste_clearance_schedule_item.recycle_type_id', 'weight']);
        return view('waste_clearance.view', compact('schedule', 'id', 'items'));
    }


    public function create()
    {
        $hubs = CollectionHub::orderBy('hub_name')->get();
        return view('waste_clearance.create', compact('hubs'));
    }

    public function insert(Request $request)
    {
        if (!$request->items) {
            return redirect()->back()->with('failMsg', 'Recycle item is required.')->withInput($request->input());
        }
        $items = $request->items;

        $this->validate($request, [
            'items' => 'required',
            'collection_time' => 'required',
            'hub' => 'required',
            'buyer_name' => 'required',
            'buyer_phone_number' => 'required',
        ]);

        $schedule_id = WasteClearanceSchedule::create([
            'collection_time' =>  $request->collection_time,
            'collection_hub_id' => $request->hub,
            'buyer_name' => $request->buyer_name,
            'buyer_phone_number' => $request->buyer_phone_number,
            'pin_code' => $request->pin_code,
            'status' => 1,
        ])->id;
        $hub = CollectionHub::find($request->hub);

        foreach ($items as $key => $value) {
            WasteClearanceScheduleItem::create([
                'waste_clearance_schedule_id' => $schedule_id,
                'recycle_type_id' => $key,
                'weight' => $value
            ]);
        }

        $date = date('Y/m/d H:i:s', strtotime($request->collection_time));
        $user_token = User::leftJoin('collector', 'users.id', '=', 'collector.user_id')->leftJoin('user_role', 'user_role.user_id', '=', 'users.id')->where('device_token', '!=', null)->where('device_token', '!=', '')->where('user_role.role_id', 3)->where('receive_notification', 1)->where('collector.collection_hub_id', $request->hub)->pluck('device_token')->all();
        $user = User::leftJoin('collector', 'users.id', '=', 'collector.user_id')->leftJoin('user_role', 'user_role.user_id', '=', 'users.id')->where('user_role.role_id', 3)->where('collector.collection_hub_id', $request->hub)->pluck('users.id')->all();

        $itemsArr = array();
        foreach ($items as $key => $value) {
            $recycle = RecycleType::find($key)->name;
            array_push($itemsArr, $recycle . '-' . $value . 'kg');
        }
        $itemsString = implode(', ', $itemsArr);

        $title = 'New waste clearance schedule';
        $body = 'A new waste clearance is scheduled on ' . $date . ' at ' .  $hub->hub_name . '. Items : ' . $itemsString;
        $notification_data = array("detail" => '');
        $user_type = 'collector';
        Helper::configNotification($user_token, $user, $title, $body, $notification_data, $user_type);

        $body = $body . '<br><br> Buyer name : ' . $request->buyer_name . '<br> Buyer phone number : ' . $request->buyer_phone_number;
        $hub_admins = User::leftJoin('hub_admin', 'users.id', '=', 'hub_admin.user_id')->leftJoin('user_role', 'user_role.user_id', '=', 'users.id')->where('user_role.role_id', 4)->where('hub_admin.collection_hub_id', $request->hub)->get();
        foreach ($hub_admins as $value) {
            Helper::sendEmail($value->email, $title, $body);
        }
        return redirect()->route('waste_clearance.index')->with('successMsg', 'Waste clearance is scheduled.');
    }

    public function edit($id)
    {
        $hubs = CollectionHub::orderBy('hub_name')->get();
        $schedule = WasteClearanceSchedule::find($id);
        if ($schedule->status != 1) {
            return redirect()->route('waste_clearance.index')->with('warningMsg', 'Only waste clearance with status pending can be edited.');
        }
        $items = WasteClearanceScheduleItem::where('waste_clearance_schedule_id', $id)->join('recycle_type', 'recycle_type.id', '=', 'waste_clearance_schedule_item.recycle_type_id')->get(['recycle_type.name', 'waste_clearance_schedule_item.recycle_type_id', 'weight']);

        return view('waste_clearance.edit', compact('schedule', 'id', 'hubs', 'items'));
    }

    public function cancel($id)
    {
        $schedule = WasteClearanceSchedule::find($id);
        if ($schedule->status == 1) {
            $schedule->status = 3;
            $schedule->save();
        } else
            return redirect()->route('waste_clearance.index')->with('warningMsg', 'Cannot be cancelled.');


        return redirect()->route('waste_clearance.index')->with('successMsg', 'Schedule is canceled.');
    }

    public function edit_db(Request $request)
    {
        $this->validate($request, [
            'collection_time' => 'required',
            'hub' => 'required',
            'buyer_name' => 'required',
            'buyer_phone_number' => 'required',
        ]);

        $schedule = WasteClearanceSchedule::find($request->schedule_id);
        $schedule->collection_time = $request->collection_time;
        $schedule->collection_hub_id = $request->hub;
        $schedule->buyer_name = $request->buyer_name;
        $schedule->buyer_phone_number = $request->buyer_phone_number;
        $schedule->status = $request->status;
        $schedule->save();

        return redirect()->route('waste_clearance.index')->with('successMsg', 'Schedule is edited.');
    }

    public function getHubInfo(Request $request)
    {
        $hub_id = $request->hub_id;
        $hub = CollectionHub::find($hub_id);
        $state = CollectionHub::find($hub_id)->state;
        $items = DB::select("SELECT recycle_type_id,name from collection_hub_recycle chr left join recycle_type rt on chr.recycle_type_id = rt.id where date(start_date) <= now() and date(end_date) >= now() and is_active = 1 and collection_hub_id = '$hub_id'");

        return array($hub, $state, $items);
    }

    public function view_statement($id)
    {
        $clearance = WasteClearanceSchedule::find($id);

        return view('waste_clearance.statement', compact('clearance'));
    }
}
