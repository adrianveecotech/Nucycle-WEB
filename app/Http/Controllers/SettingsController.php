<?php

namespace App\Http\Controllers;

use App\Models\Merchant;
use App\Models\RecycleCategory;
use App\Models\RecycleType;
use App\Models\UserRole;
use App\Models\State;
use App\Models\RewardsCategory;
use App\Models\BannerTag;
use App\Models\City;
use App\Models\RecycleCategoryStatisticIndicator;
use App\Models\StatisticIndicator;
use Illuminate\Http\Request;

class SettingsController extends Controller
{
    public function index()
    {
        return view('settings.index');
    }

    public function user_role()
    {
        $roles = UserRole::get();

        return view('settings.user_role.index', compact('roles'));
    }

    public function user_role_edit($id)
    {
        $role = UserRole::find($id);

        return view('settings.user_role.edit', compact('role', 'id'));
    }

    public function user_role_view($id)
    {
        $role = UserRole::find($id);

        return view('settings.user_role.view', compact('role', 'id'));
    }

    public function user_role_edit_db(Request $request)
    {
        $this->validate($request, [
            'role' => 'required',
        ]);
        $role = UserRole::find($request->id);

        $role->role = $request->role;
        $role->save();

        return redirect()->route('settings.user_role')->with('successMsg', 'Role is edited.');
    }

    public function recycle_category()
    {
        $recycle_categories = RecycleCategory::get();
        $indicators = StatisticIndicator::get();
        $category_indicator_values = RecycleCategoryStatisticIndicator::get();
        return view('settings.recycle_category.index', compact('recycle_categories', 'indicators', 'category_indicator_values'));
    }

    public function recycle_category_create()
    {
        $indicator_values = StatisticIndicator::get();
        return view('settings.recycle_category.create', compact('indicator_values'));
    }

    public function recycle_category_insert(Request $request)
    {
        $this->validate($request, [
            'name' => 'required',
            'id1' => 'required',
            'id2' =>  'required',
            'id3' =>  'required',
            'id4' =>  'required',
            // 'cars_removed' => 'required|required',
            // 'household_saving' =>  'required|required',
            // 'water_saved' =>  'required|required',
            // 'wheelie_bins' =>  'required|required',
        ]);

        $category_id = RecycleCategory::create([
            'name' => $request->name,
        ])->id;
        for ($i = 1; $i <= 4; $i++) {
            $x = 'id' . $i;
            RecycleCategoryStatisticIndicator::create([
                'recycle_category_id' => $category_id,
                'indicator_id' => $i,
                'value' => $request->$x
            ]);
        }

        return redirect()->route('settings.recycle_category')->with('successMsg', 'Recycle category is created.');
    }

    public function recycle_category_edit($id)
    {
        $recycle_category = RecycleCategory::find($id);

        return view('settings.recycle_category.edit', compact('recycle_category', 'id'));
    }

    public function recycle_category_view($id)
    {
        $recycle_category = RecycleCategory::find($id);
        return view('settings.recycle_category.view', compact('recycle_category', 'id'));
    }

    public function recycle_category_edit_db(Request $request)
    {

        $this->validate($request, [
            'name' => 'required',
            'id1' => 'required',
            'id2' =>  'required',
            'id3' =>  'required',
            'id4' =>  'required',
        ]);

        $recycle_category = RecycleCategory::where('id', '=', $request->id)->first();
        $recycle_category->name = $request->name;
        $recycle_category->save();
        // $recycle_category->cars_removed = $request->cars_removed;
        // $recycle_category->household_saving = $request->household_saving;
        // $recycle_category->water_saved = $request->water_saved;
        // $recycle_category->wheelie_bins = $request->wheelie_bins;
        for ($i = 1; $i <= 4; $i++) {
            $statistic = RecycleCategoryStatisticIndicator::where('recycle_category_id', $request->id)->where('indicator_id', $i)->first();
            $x = 'id' . $i;
            $statistic->value = $request->$x;
            $statistic->save();
        }

        return redirect()->route('settings.recycle_category')->with('successMsg', 'Recycle category is edited.');
    }

    public function recycle_category_delete($id)
    {

        $recycle_category = RecycleCategory::where('id', '=', $id)->first();
        $recycle_category->delete();

        return redirect()->route('settings.recycle_category')->with('successMsg', 'Recycle category is deleted.');
    }

    public function recycle_type()
    {
        $recycle_types = RecycleType::get();

        return view('settings.recycle_type.index', compact('recycle_types'));
    }

    public function recycle_type_create()
    {
        $recycle_categories = RecycleCategory::get();
        return view('settings.recycle_type.create', compact('recycle_categories'));
    }

    public function recycle_type_insert(Request $request)
    {

        $this->validate($request, [
            'name' => 'required',
            'recycle_category_id' => 'required'
        ]);

        RecycleType::create([
            'name' => $request->name,
            'recycle_category_id' => $request->recycle_category_id
        ]);
        return redirect()->route('settings.recycle_type')->with('successMsg', 'Recycle type is created.');
    }

    public function recycle_type_edit($id)
    {
        $recycle_type = RecycleType::find($id);

        return view('settings.recycle_type.edit', compact('recycle_type', 'id'));
    }

    public function recycle_type_view($id)
    {
        $recycle_type = RecycleType::find($id);

        return view('settings.recycle_type.view', compact('recycle_type', 'id'));
    }

    public function recycle_type_edit_db(Request $request)
    {
        $this->validate($request, [
            'name' => 'required',
        ]);

        $recycle_type = RecycleType::where('id', '=', $request->id)->first();
        $recycle_type->name = $request->name;
        $recycle_type->save();

        return redirect()->route('settings.recycle_type')->with('successMsg', 'Recycle type is edited.');
    }

    public function recycle_type_delete($id)
    {

        $recycle_type = RecycleType::where('id', '=', $id)->first();
        $recycle_type->delete();

        return redirect()->route('settings.recycle_type')->with('successMsg', 'Recycle category is deleted.');
    }

    public function merchant()
    {
        $merchants = Merchant::get();

        return view('settings.merchant.index', compact('merchants'));
    }

    public function merchant_create()
    {
        $states = State::get();
        $cities = City::get();
        return view('settings.merchant.create', compact('states','cities'));
    }

    public function merchant_insert(Request $request)
    {
        $this->validate($request, [
            'name' => 'required',
            'address' => 'required',
            'postcode' => 'required|integer|digits:5',
            'phone_number' => 'required',
            'email' => 'required',
            'url' => 'url',
        ]);

        Merchant::create([
            'name' => $request->name,
            'address' => $request->address,
            'postcode' => $request->postcode,
            'state_id' => $request->state_id,
            'city_id' => $request->city_id,
            'phone_number' => $request->phone_number,
            'email' => $request->email,
            'url' => $request->url,
            'is_active' => $request->has('active_status') ? 1 :  0,
            'basic_report' => $request->has('basic_report') ? 1 :  0,
            'ads_report' => $request->has('ads_report') ? 1 :  0,
            'subscription_report' => $request->has('subscription_report') ? 1 :  0,

        ]);
        return redirect()->route('settings.merchant')->with('successMsg', 'Merchant is created.');
    }

    public function merchant_edit($id)
    {
        $merchant = Merchant::find($id);
        $states = State::get();
        $cities = City::get();
        return view('settings.merchant.edit', compact('merchant', 'id', 'states','cities'));
    }

    public function merchant_view($id)
    {
        $merchant = Merchant::find($id);
        $states = State::get();
        return view('settings.merchant.view', compact('merchant', 'id', 'states'));
    }

    public function merchant_edit_db(Request $request)
    {
        $this->validate($request, [
            'name' => 'required',
            'address' => 'required',
            'postcode' => 'required|integer|digits:5',
            'phone_number' => 'required',
            'email' => 'required',
            'url' => 'url',
        ]);

        $merchant = Merchant::where('id', '=', $request->id)->first();
        $merchant->name = $request->name;
        $merchant->address = $request->address;
        $merchant->postcode = $request->postcode;
        $merchant->state_id = $request->state_id;
        $merchant->city_id = $request->city_id;
        $merchant->phone_number = $request->phone_number;
        $merchant->email = $request->email;
        $merchant->url = $request->url;
        $merchant->is_active = $request->active_status == 'active' ? 1 : 0;
        $merchant->basic_report = $request->basic_report == 'active' ? 1 :  0;
        $merchant->ads_report = $request->ads_report == 'active' ? 1 :  0;
        $merchant->subscription_report = $request->subscription_report == 'active' ? 1 :  0;
        $merchant->save();

        return redirect()->route('settings.merchant')->with('successMsg', 'Merchant is edited.');
    }

    public function merchant_delete($id)
    {

        $merchant = Merchant::where('id', '=', $id)->first();
        $merchant->delete();

        return redirect()->route('settings.merchant')->with('successMsg', 'Merchant is deleted.');
    }

    public function banner_tag()
    {
        $banner_tags = BannerTag::get();

        return view('settings.banner_tag.index', compact('banner_tags'));
    }

    public function banner_tag_create()
    {
        return view('settings.banner_tag.create');
    }

    public function banner_tag_insert(Request $request)
    {
        $this->validate($request, [
            'name' => 'required',
        ]);

        $active = '';
        $request->has('active_status') ? $active = 1 : $active = 0;

        BannerTag::create([
            'name' => $request->name,
            'is_active' => $active,
        ]);
        return redirect()->route('settings.banner_tag')->with('successMsg', 'BannerTag is created.');
    }

    public function banner_tag_edit($id)
    {
        $banner_tag = BannerTag::find($id);
        return view('settings.banner_tag.edit', compact('banner_tag', 'id'));
    }

    public function banner_tag_view($id)
    {
        $banner_tag = BannerTag::find($id);
        return view('settings.banner_tag.view', compact('banner_tag', 'id'));
    }

    public function banner_tag_edit_db(Request $request)
    {
        $this->validate($request, [
            'name' => 'required',
        ]);

        $banner_tag = BannerTag::where('id', '=', $request->id)->first();
        $banner_tag->name = $request->name;
        $banner_tag->is_active = $request->active_status == 'active' ? 1 : 0;
        $banner_tag->save();

        return redirect()->route('settings.banner_tag')->with('successMsg', 'BannerTag is edited.');
    }

    public function banner_tag_delete($id)
    {

        $banner_tag = BannerTag::where('id', '=', $id)->first();
        $banner_tag->delete();

        return redirect()->route('settings.banner_tag')->with('successMsg', 'BannerTag is deleted.');
    }

    public function rewards_category()
    {
        $rewards_categories = RewardsCategory::get();

        return view('settings.rewards_category.index', compact('rewards_categories'));
    }

    public function rewards_category_create()
    {
        return view('settings.rewards_category.create');
    }

    public function rewards_category_insert(Request $request)
    {
        $this->validate($request, [
            'name' => 'required',
        ]);

        RewardsCategory::create([
            'name' => $request->name,
        ]);
        return redirect()->route('settings.rewards_category')->with('successMsg', 'Rewards Category is created.');
    }

    public function rewards_category_edit($id)
    {
        $rewards_category = RewardsCategory::find($id);
        return view('settings.rewards_category.edit', compact('rewards_category', 'id'));
    }

    public function rewards_category_view($id)
    {
        $rewards_category = RewardsCategory::find($id);
        return view('settings.rewards_category.view', compact('rewards_category', 'id'));
    }

    public function rewards_category_edit_db(Request $request)
    {
        $this->validate($request, [
            'name' => 'required',
        ]);

        $rewards_category = RewardsCategory::where('id', '=', $request->id)->first();
        $rewards_category->name = $request->name;
        $rewards_category->save();

        return redirect()->route('settings.rewards_category')->with('successMsg', 'Rewards Category is edited.');
    }

    public function rewards_category_delete($id)
    {

        $rewards_category = RewardsCategory::where('id', '=', $id)->first();
        $rewards_category->delete();

        return redirect()->route('settings.rewards_category')->with('successMsg', 'Rewards Category is deleted.');
    }

    public function statistic_indicator()
    {
        $indicators = StatisticIndicator::get();

        return view('settings.statistic_indicator.index', compact('indicators'));
    }


    public function statistic_indicator_edit($id)
    {
        $indicator = StatisticIndicator::find($id);
        return view('settings.statistic_indicator.edit', compact('indicator', 'id'));
    }

    public function statistic_indicator_view($id)
    {
        $indicator = StatisticIndicator::find($id);
        return view('settings.statistic_indicator.view', compact('indicator', 'id'));
    }

    public function statistic_indicator_edit_db(Request $request)
    {
        $this->validate($request, [
            'name' => 'required',
        ]);

        $indicator = StatisticIndicator::where('id', '=', $request->id)->first();
        $indicator->name = $request->name;
        $indicator->save();

        return redirect()->route('settings.statistic_indicator')->with('successMsg', 'Indicator is edited.');
    }
}
