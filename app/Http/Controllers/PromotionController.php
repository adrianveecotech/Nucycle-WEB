<?php

namespace App\Http\Controllers;

use App\Models\BannerTag;
use App\Models\Merchant;
use App\Models\Promotion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class PromotionController extends Controller
{
    public function index(Request $request)
    {
        $promotions = Promotion::get();
        return view('promotion.index', compact('promotions'));
    }

    public function create()
    {
        $merchants = Merchant::get();
        $banners = BannerTag::get();
        return view('promotion.create', compact('banners','merchants'));
    }

    public function edit($id)
    {
        $promotion = Promotion::find($id);
        $banners = BannerTag::get();
        $merchants = Merchant::get();
        return view('promotion.edit', compact('promotion', 'banners', 'id','merchants'));
    }

    public function view($id)
    {
        $promotion = Promotion::find($id);
        $banners = BannerTag::get();
        return view('promotion.view', compact('promotion', 'banners', 'id'));
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
            $file->move('nucycle-admin/images/promotion_image', $image);
        }

        Promotion::create([
            'title' =>  $request->title,
            'description' => $request->description,
            'image' => $image,
            'banner_tag_id' => $request->banner,
            'merchant_id' => $request->merchant,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'status' => $request->status == "Draft" ? 0 : 1,
        ]);

        return redirect()->route('promotion.index')->with('successMsg', 'Promotion is created.');
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

        $promotion = Promotion::find($request->promotion_id);

        $image = '';
        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $image = date('YmdHis') . "." . $file->getClientOriginalExtension();
            $file->move('nucycle-admin/images/promotion_image', $image);
            File::delete('nucycle-admin/images/promotion_image/' . $promotion->image);
        } else {
            $image = $promotion->image;
        }

        $promotion->title = $request->title;
        $promotion->description = $request->description;
        $promotion->image = $image;
        $promotion->banner_tag_id = $request->banner;
        $promotion->merchant_id = $request->merchant;
        $promotion->start_date = $request->start_date;
        $promotion->end_date = $request->end_date;
        $promotion->status = $request->status == "Draft" ? 0 : 1;
        $promotion->save();

        return redirect()->route('promotion.index')->with('successMsg', 'Promotion is edited.');
    }

    public function delete($id)
    {
        $promotion = Promotion::find($id);
        File::delete('nucycle-admin/images/promotion_image/' . $promotion->image);
        $promotion->delete();
        return redirect()->route('promotion.index')->with('successMsg', 'Promotion is deleted.');
    }
}
