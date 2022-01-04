<?php

namespace App\Http\Controllers;

use App\Models\BannerTag;
use App\Models\Guideline;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class GuidelineController extends Controller
{
    public function index(Request $request)
    {
        $guidelines = Guideline::get();
        return view('guideline.index', compact('guidelines'));
    }

    public function create()
    {
        $banners = BannerTag::get();
        return view('guideline.create', compact('banners'));
    }

    public function view($id)
    {
        $guideline = Guideline::find($id);
        $banners = BannerTag::get();
        return view('guideline.view', compact('guideline', 'banners', 'id'));
    }

    public function edit($id)
    {
        $guideline = Guideline::find($id);
        $banners = BannerTag::get();
        return view('guideline.edit', compact('guideline', 'banners', 'id'));
    }

    public function insert(Request $request)
    {
        $this->validate($request, [
            'title' => 'required',
            'description' => 'required',
            'image' => 'required',
            'start_date' => 'required',
            'end_date' => 'required',
        ]);

        $image = '';
        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $image = date('YmdHis') . "." . $file->getClientOriginalExtension();
            $file->move('nucycle-admin/images/guideline_image', $image);
        }

        $target = '';

        if ($request->for_customer && $request->for_collector)
            $target = $request->for_customer . ',' . $request->for_collector;
        else if ($request->for_customer)
            $target = $request->for_customer;
        else if ($request->for_collector)
            $target = $request->for_collector;

        Guideline::create([
            'title' =>  $request->title,
            'target_role' => $target,
            'description' => $request->description,
            'image' => $image,
            'banner_tag_id' => $request->banner,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'status' => $request->status == "Draft" ? 0 : 1,
        ]);

        return redirect()->route('guideline.index')->with('successMsg', 'Guideline is created.');
    }

    public function edit_db(Request $request)
    {
        $this->validate($request, [
            'title' => 'required',
            'description' => 'required',
            'start_date' => 'required',
            'end_date' => 'required',
        ]);

        $target = '';

        if ($request->for_customer && $request->for_collector)
            $target = $request->for_customer . ',' . $request->for_collector;
        else if ($request->for_customer)
            $target = $request->for_customer;
        else if ($request->for_collector)
            $target = $request->for_collector;

        $guideline = Guideline::find($request->guideline_id);

        $image = '';
        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $image = date('YmdHis') . "." . $file->getClientOriginalExtension();
            $file->move('nucycle-admin/images/guideline_image', $image);
            File::delete('nucycle-admin/images/guideline_image/' . $guideline->image);
        } else {
            $image = $guideline->image;
        }

        $guideline->title = $request->title;
        $guideline->description = $request->description;
        $guideline->target_role = $target;
        $guideline->image = $image;
        $guideline->banner_tag_id = $request->banner;
        $guideline->start_date = $request->start_date;
        $guideline->end_date = $request->end_date;
        $guideline->status = $request->status == "Draft" ? 0 : 1;
        $guideline->save();

        return redirect()->route('guideline.index')->with('successMsg', 'Guideline is edited.');
    }

    public function delete($id)
    {
        $guideline = Guideline::find($id);
        File::delete('nucycle-admin/images/guideline_image/' . $guideline->image);
        $guideline->delete();
        return redirect()->route('guideline.index')->with('successMsg', 'Guideline is deleted.');
    }
}
