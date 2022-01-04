<?php

namespace App\Http\Controllers;

use App\Helper\Helper;
use App\Models\CollectionHub;
use App\Models\CollectionHubRecycleType;
use App\Models\ContactUsInfo;
use App\Models\User;
use App\Models\WasteClearanceSchedule;
use App\Models\WasteClearanceItem;
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
}
