<?php

namespace App\Http\Controllers;

use App\Helper\Helper;
use App\Models\CollectionHub;
use App\Models\CollectionHubRecycleType;
use App\Models\ContactUsInfo;
use App\Models\User;
use App\Models\WasteClearanceSchedule;
use App\Models\WasteClearanceItem;
use App\Models\WasteClearanceSchedulePayment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class WasteClearanceStatementController extends Controller
{
    public function index()
    {
        $schedules = WasteClearanceSchedule::where('status', 2)->get();
        return view('waste_clearance_statement.index', compact('schedules'));
    }

    public function view($id)
    {
        $schedule = WasteClearanceSchedule::find($id);
        $company_info = ContactUsInfo::first();
        $items = WasteClearanceItem::where('waste_clearance_schedule_id', $id)->join('recycle_type', 'recycle_type.id', '=', 'waste_clearance_item.recycle_type_id')->get(['recycle_type.name', 'waste_clearance_item.recycle_type_id', 'weight']);
        return view('waste_clearance_statement.view', compact('schedule', 'id', 'items','company_info'));
    }

    public function payment($id)
    {
        $items = WasteClearanceItem::where('waste_clearance_schedule_id', $id)->join('recycle_type', 'recycle_type.id', '=', 'waste_clearance_item.recycle_type_id')->get(['recycle_type.name', 'waste_clearance_item.recycle_type_id', 'weight']);
        return view('waste_clearance_statement.payment',compact('id','items'));
    }

    public function insert_payment(Request $request)
    {
        $request->validate([
            'image' => 'required|file|mimes:pdf,png,jpg,jpeg|max:3072'
        ]);
        $receipt = new WasteClearanceSchedulePayment();
        $image = '';
        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $image = date('YmdHis') . "." . $file->getClientOriginalExtension();
            $file->move('nucycle-admin/images/receipt', $image);
        }
        $item = array_combine($request->input('unitname'),$request->input('unitprice'));
        $itemlist = json_encode($item);
        $receipt->invoice_date = $request->input('invoice_date');
        $receipt->unit_price = $itemlist;
        $receipt->total_price = $request->input('total_price');
        $receipt->receipt_date = $request->input('receipt_date');
        $receipt->receipt_number = $request->input('receipt_number');
        $receipt->total_amount = $request->input('total_amount');
        $receipt->image = $image;
        $receipt->waste_clearance_schedule_id = $request->input('statement_id');
        $receipt->save();
        //return back()->with('successMsg','Receipt have been successfully uploaded');
        return redirect()->route('waste_clearance_statement.index');
        
    }
}
