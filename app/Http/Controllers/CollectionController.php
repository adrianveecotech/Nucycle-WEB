<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Collection;
use App\Models\CollectionDetail;
use App\Models\CollectionHub;
use App\Models\CollectionHubRecycleType;
use App\Models\Collector;
use App\Models\Customer;
use App\Models\CustomerMembership;
use App\Models\CustomerPointTransaction;
use App\Models\Level;
use Auth;

class CollectionController extends Controller
{
    public function index(Request $request)
    {
        if (in_array(4, Auth::user()->users_roles_id())) {
            $hub_id = (Auth::user()->hub_admin->collection_hub_id);
            $collections = Collection::where('collection_hub_id', $hub_id)->orderBy('created_at', 'DESC')->get();
            return view('collection.index', compact('collections'));
        } else {
            $collections = Collection::orderBy('created_at', 'DESC')->get();
            return view('collection.index', compact('collections'));
        }
    }

    public function view($id)
    {
        $collection = Collection::find($id);
        return view('collection.view', compact('collection', 'id'));
    }

    public function edit($id)
    {
        $collection = Collection::find($id);
        $customers = Customer::orderBy('name', 'ASC')->get();
        $collectors = Collector::orderBy('name', 'ASC')->get();
        $hubs = CollectionHub::orderBy('hub_name', 'ASC')->get();
        $collection_item_detail = array();
        foreach ($collection->collection_detail as $key => $value) {
           $collection_item_detail[] = CollectionHubRecycleType::where('recycle_type_id',$value->recycling_type_id)->where('collection_hub_id',$collection->collection_hub_id)->where('is_active',1)->whereDate('start_date','<=',date('Y-m-d'))->whereDate('end_date','>=',date('Y-m-d'))->first();
        }
        $pointBeforeOrder = CustomerMembership::where('customer_id',$collection->customer_id)->first()->points - $collection->all_point;
        $multiplier = Level::where('points_from','<=',$pointBeforeOrder)->where('points_to','>=',$pointBeforeOrder)->first()->multiplier;
        return view('collection.edit', compact('collection', 'customers', 'collectors', 'id', 'hubs','collection_item_detail','multiplier','pointBeforeOrder'));
    }

    public function edit_db(Request $request)
    {
        $this->validate($request, [
            'customer' => 'required',
            'collector' => 'required',
            'hub' => 'required',
            'total_point' => 'required|numeric',
            'bonus_point' => 'required|numeric',
            'all_point' => 'required|numeric',
            'total_weight' => 'required',
        ]);

        $item_ids = $request->item_id;
        $item_weights = $request->item_weight;
        $item_points = $request->item_point;
        $allPoint = $request->all_point;
        $collection = Collection::find($request->collection_id);
        $collection->customer_id = $request->customer;
        $collection->collection_hub_id = $request->hub;
        $collection->collector_id = $request->collector;
        $collection->total_point = $request->total_point;
        $collection->bonus_point = $request->bonus_point;
        $collection->all_point = $allPoint;
        $collection->total_weight = $request->total_weight;
        foreach ($item_ids as $key => $id) {
            $collection_detail = CollectionDetail::find($id);
            $collection_detail->weight = $item_weights[$key];
            $collection_detail->total_point = $item_points[$key];
            $collection_detail->save();
        }
        $collection->save();

        $membership = CustomerMembership::where('customer_id',$request->customer)->first();
        $membership->points = $request->point_before_order + $allPoint;

        $newLevel = Level::where('points_from','<=',$membership->points)->where('points_to','>=',$membership->points)->first()->id;
        $membership->level_id = $newLevel;
        $membership->save();

        $pointNeeded = 0;
        
        $pointTransaction = CustomerPointTransaction::where('description','collection')->where('value',$request->collection_id)->first();
        if($pointTransaction->point == $pointTransaction->balance){
            $pointTransaction->point = $allPoint;
            $pointTransaction->balance = $allPoint;
            $pointTransaction->save();
        }else if($pointTransaction->point > $pointTransaction->balance){
            $pointUsed = $pointTransaction->point - $pointTransaction->balance;
            if($pointUsed <= $allPoint){
                $pointTransaction->point = $allPoint;
                $pointTransaction->balance = $allPoint - $pointUsed;
                $pointTransaction->save();
            }else if($pointUsed > $allPoint){
                $pointNeeded = $pointUsed - $allPoint;
                $pointTransaction->point = $allPoint;
                $pointTransaction->balance = 0;
                $pointTransaction->save();

                $pointTransactionNext = CustomerPointTransaction::where('description','collection')->where('point','>',0)->where('balance','!=',0)->whereDate('expiration_date','>=',date('Y-m-d'))->where('customer_id',$request->customer)->where('status',1)->get();
                foreach ($pointTransactionNext as $value) {
                    $balance = $value->balance;
                    $pointID = $value->id;
                    if($balance < $pointNeeded){
                        $pointNeeded = $pointNeeded - $balance;
                        $currentTransaction = CustomerPointTransaction::find($pointID);
                        $currentTransaction->balance = 0;
                        $currentTransaction->save();
                    }else if($balance >= $pointNeeded){
                        $currentTransaction = CustomerPointTransaction::find($pointID);
                        $currentTransaction->balance = $currentTransaction->balance - $pointNeeded;
                        $currentTransaction->save();
                        break;
                    }
                }

            }
        }
        
        return redirect()->route('collection.index')->with('successMsg', 'Collection is edited.');
    }

    public function cancel($id)
    {
        $collection = Collection::find($id);
        if ($collection->status == 0)
            abort('404');

        $point = CustomerPointTransaction::where('description', 'collection')->where('value', $id)->first();
        if ($point->balance > 0 && $point->expiration_date >= date('Y-m-d')) {
            $customer = CustomerMembership::where('customer_id', $collection->customer_id)->first();
            $customer->points = round($customer->points - $point->balance,2);
            $level = Level::where('points_from', '<=', $customer->points)->where('points_to', '>=', $customer->points)->first();
            $customer->level_id = $level->id;
            $customer->save();

            $point->status = 2;
            $point->save();

            CustomerPointTransaction::create([
                'customer_id' => $collection->customer_id,
                'point' => -$point->balance,
                'description' => 'cancelled',
                'value' => $id,
            ]);
        }

        // $point = $collection->usable_point;
        // $customer = CustomerMembership::where('customer_id', $collection->customer_id)->first();
        // $customer->points = $customer->points - $point;

        $collection->status = 0;
        $collection->cancelled_at = date("Y-m-d H:i:s");

        $collection->save();

        return redirect()->route('collection.index')->with('successMsg', 'Collection is cancelled.');
    }
}
