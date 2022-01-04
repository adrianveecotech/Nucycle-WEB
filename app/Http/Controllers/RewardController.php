<?php

namespace App\Http\Controllers;

use App\Models\Merchant;
use App\Models\Reward;
use App\Models\RewardsCategory;
use App\Models\RewardTag;
use App\Models\Voucher;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;

class RewardController extends Controller
{
    public function index(Request $request)
    {
        $rewards1 = Reward::get();
        // dd($rewards1);
        // $rewards = array();
        // foreach ($rewards1 as $element) {
        //     $rewards[$element['merchant_id']][$element['reward']][] = $element;
        // }
        $merchants = Merchant::get()->toArray();
        return view('reward.index', compact('rewards1', 'merchants'));
    }

    public function create()
    {
        $merchants = Merchant::get();
        $categories = RewardsCategory::get();
        return view('reward.create', compact('merchants', 'categories'));
    }

    public function insert(Request $request)
    {

        $tags = implode(';', array_map('trim', explode(';', $request->tag)));
        $this->validate($request, [
            'category' => 'required',
            'merchant' => 'required',
            'title' => 'required',
            'image' => 'required',
            'point' => 'required',
            'redemption' => 'required',
            'description' => 'required',
            'start_date' => 'required',
            'end_date' => 'required',
            'status' => 'required',
        ]);

        $image = '';
        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $image = date('YmdHis') . "." . $file->getClientOriginalExtension();
            $file->move('nucycle-admin/images/reward_image', $image);
        }

        $reward = Reward::create([
            'merchant_id' => $request->merchant,
            'reward_category_id' => $request->category,
            'title' => $request->title,
            'image' => $image,
            'point' => $request->point,
            'redemption_per_user' => $request->redemption_per_user,
            'description' => $request->description,
            'tag' => $tags,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'terms' => $request->terms,
            'status' => $request->status == "Draft" ? 0 : 1,
        ]);

        if ($request->is_redeem) {
            foreach ($request->code as $key => $value) {
                Voucher::create([
                    'reward_id' => $reward->id,
                    'code' => $value,
                    'is_redeem' => in_array("active" . $key, $request->is_redeem) ? 1 : 0,
                    'expiry_date' => $request->end_date
                ]);
            }
        } else {
            foreach ($request->code as $key => $value) {
                Voucher::create([
                    'reward_id' => $reward->id,
                    'code' => $value,
                    'is_redeem' => 0,
                    'expiry_date' => $request->end_date
                ]);
            }
        }


        return redirect()->route('reward.index')->with('successMsg', 'Reward is created.');
    }

    public function edit($id)
    {
        $reward = Reward::find($id);
        $merchants = Merchant::get();
        $categories = RewardsCategory::get();
        $temp_tag = array();
        $reward->tag = implode(' ; ', explode(';', $reward['tag']));
        return view('reward.edit', compact('reward', 'merchants', 'categories', 'id'));
    }

    public function view($id)
    {
        $reward = Reward::find($id);
        $merchants = Merchant::get();
        $categories = RewardsCategory::get();
        $temp_tag = array();
        $reward->tag = implode(' ; ', explode(';', $reward['tag']));
        return view('reward.view', compact('reward', 'merchants', 'categories', 'id'));
    }

    public function edit_db(Request $request)
    {
        $tags = implode(';', array_map('trim', explode(';', $request->tag)));
        $reward = Reward::find($request->reward_id);

        $image = '';
        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $image = date('YmdHis') . "." . $file->getClientOriginalExtension();
            $file->move('nucycle-admin/images/reward_image', $image);
        }

        $this->validate($request, [
            'category' => 'required',
            'merchant' => 'required',
            'title' => 'required',
            'point' => 'required',
            'redemption' => 'required',
            'description' => 'required',
            'start_date' => 'required',
            'end_date' => 'required',
            'status' => 'required',
        ]);
        $status = $request->status;
        $status_id = '';
        if ($status == 'Publish') {
            $status_id = 1;
        } elseif ($status == 'Draft') {
            $status_id = 0;
        } elseif ($status == 'Expired') {
            $status_id = 2;
        };
        $reward->reward_category_id = $request->category;
        $reward->merchant_id = $request->merchant;
        $reward->title = $request->title;
        if ($request->hasFile('image')) {
            File::delete('nucycle-admin/images/reward_image/' . $reward->image);
            $reward->image = $image;
        }
        if ($request->status == "Draft")

            $reward->point = $request->point;
        $reward->redemption_per_user = $request->redemption;
        $reward->description = $request->description;
        $reward->tag = $tags;
        $reward->start_date = $request->start_date;
        $reward->end_date = $request->end_date;
        $reward->terms = $request->terms;
        $reward->status = $status_id;
        $reward->save();


        return redirect()->route('reward.index')->with('successMsg', 'Collection Hub Recycle Type is edited.');
    }

    public function delete($id)
    {
        $reward = Reward::where('id', $id)->first();
        File::delete('nucycle-admin/images/reward_image/' . $reward->image);
        $reward->delete();

        return redirect()->route('reward.index')->with('successMsg', 'Reward is deleted.');
    }

    public function get_reward_by_merchant(Request $request)
    {
        if ($request->merchant_id == 'Select a merchant') {
            return;
        }
        $status = $request->status_filter;
        $status_id = '';
        if ($status == 'Active') {
            $status_id = 1;
        } elseif ($status == 'Draft') {
            $status_id = 0;
        } elseif ($status == 'Expired') {
            $status_id = 2;
        };

        $merchant_rewards = Reward::where('merchant_id', $request->merchant_id)->where('status', $status_id)->get();
        $html = '';

        foreach ($merchant_rewards as $merchant_reward) {
            $imageSource = env('APP_URL') . '/nucycle-admin/images/reward_image/' . $merchant_reward->image;
            $html .= '<tr>';
            $html .= '<td class="lalign">';
            $html .= $merchant_reward->title;
            $html .= '</td> <td>';
            $html .= $merchant_reward->reward_category->name . '<br>';
            $html .= '</td><td ><img width="100%"  src="' . $imageSource . '" ><br>';
            $html .= '</td><td>';
            $html .= $merchant_reward->point . '<br>';
            $html .= '</td><td>';
            $html .= $merchant_reward->redemption_per_user . '<br>';
            $html .= '</td><td>';
            // $html .= $merchant_reward->description . '<br>';
            // $html .= '</td><td>';
            // foreach (explode(";", $merchant_reward->tag) as $tag)
            //     $html .= RewardTag::find($tag)->name . '<br>';
            // $html .= '</td><td>';
            foreach (explode(";", $merchant_reward->tag) as $tag)
                $html .= $tag  . ',<br>';
            $html .= '</td><td>';
            // $html .= $merchant_reward->tag . '<br>';
            // $html .= '</td><td>';
            $html .= $merchant_reward->start_date . '<br>';
            $html .= '</td><td>';
            $html .= $merchant_reward->end_date . '<br>';
            $html .= '</td><td>';
            // $html .= $merchant_reward->terms . '<br>';
            // $html .= '</td><td>';
            if ($merchant_reward->status == 0)
                $html .= 'Drafting';
            elseif ($merchant_reward->status == 1)
                $html .= 'Published';
            elseif ($merchant_reward->status == 2)
                $html .= 'Expired';
            $html .= '<br>';
            $html .= '</td>';
            $html .= '<td>';
            $html .= '<a href="' . route("reward.view", ["id" => $merchant_reward->id]) . '" class="btn btn-xs btn-success"><i class="nav-icon fa fa-eye"></i></a>';
            $html .= '<a href="' . route("reward.edit", ["id" => $merchant_reward->id]) . '" class="btn btn-xs btn-success"><i class="nav-icon fa fa-pencil"></i></a>';
            // $html .= '<a href="' . route("voucher.view", ["id" => $merchant_reward->id]) . '" class="btn btn-xs btn-success">View Voucher</a>';
            $html .= '<a href="' . route("reward.delete", ["id" => $merchant_reward->id]) . '" class="btn btn-xs btn-danger"><i class="nav-icon fa fa-trash"></i></a>';
            $html .= '<a href="' . route("voucher.view", ["id" => $merchant_reward->id]) . '" class="btn btn-xs btn-success">Voucher</a>';
            $html .= '</td>';
            $html .= '</tr>';
        }
        return response()->json(['html' => $html]);
    }
}
