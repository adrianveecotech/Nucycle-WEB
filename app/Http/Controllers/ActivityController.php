<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use App\Models\BannerTag;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class ActivityController extends Controller
{
    public function index(Request $request)
    {
        $activities = Activity::get();
        return view('activity.index', compact('activities'));
    }

    public function create()
    {
        $banners = BannerTag::get();
        return view('activity.create', compact('banners'));
    }

    public function edit($id)
    {
        $activity = Activity::find($id);
        $banners = BannerTag::get();
        return view('activity.edit', compact('activity', 'banners', 'id'));
    }

    public function view($id)
    {
        $activity = Activity::find($id);
        return view('activity.view', compact('activity', 'id'));
    }

    public function insert(Request $request)
    {
        $this->validate($request, [
            'title' => 'required',
            'description' => 'required',
            'image' => 'required',
            
            
            'banner' => 'required',
            'start_date' => 'required',
            'end_date' => 'required',
        ]);

        $image = '';
        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $image = date('YmdHis') . "." . $file->getClientOriginalExtension();
            $file->move('nucycle-admin/images/activity_image', $image);
        }

        Activity::create([
            'title' =>  $request->title,
            'description' => $request->description,
            'image' => $image,
            'banner_tag_id' => $request->banner,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'status' => $request->status == "Draft" ? 0 : 1,
        ]);
        return redirect()->route('activity.index')->with('successMsg', 'Activity is created.');
    }

    public function edit_db(Request $request)
    {
        $this->validate($request, [
            'title' => 'required',
            'description' => 'required',
            
            
            'banner' => 'required',
            'start_date' => 'required',
            'end_date' => 'required',
        ]);

        $activity = Activity::find($request->activity_id);

        $image = '';
        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $image = date('YmdHis') . "." . $file->getClientOriginalExtension();
            $file->move('nucycle-admin/images/activity_image', $image);
            File::delete('nucycle-admin/images/activity_image/' . $activity->image);
        } else {
            $image = $activity->image;
        }

        $activity->title = $request->title;
        $activity->description = $request->description;
        $activity->image = $image;
        $activity->banner_tag_id = $request->banner;
        $activity->start_date = $request->start_date;
        $activity->end_date = $request->end_date;
        $activity->status = $request->status == "Draft" ? 0 : 1;
        $activity->save();

        return redirect()->route('activity.index')->with('successMsg', 'Activity is edited.');
    }

    public function delete($id)
    {
        $activity = Activity::find($id);
        File::delete('nucycle-admin/images/activity_image/' . $activity->image);
        $activity->delete();

        return redirect()->route('activity.index')->with('successMsg', 'Activity is deleted.');
    }
}
