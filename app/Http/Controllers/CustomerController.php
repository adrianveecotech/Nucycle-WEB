<?php

namespace App\Http\Controllers;

use App\Models\City;
use App\Models\Customer;
use App\Models\State;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class CustomerController extends Controller
{
    public function index(Request $request)
    {
        $customers = Customer::get();
        return view('customer.index', compact('customers'));
    }

    public function create()
    {
        return view('customer.create', compact('banners'));
    }

    public function edit($id)
    {
        $customer = Customer::find($id);
        $states = State::get();
        $cities = City::get();
        return view('customer.edit', compact('customer', 'states', 'cities', 'id'));
    }

    public function view($id)
    {
        $customer = Customer::find($id);
        return view('customer.view', compact('customer', 'id'));
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
            $file->move('nucycle-admin/images/customer_image', $image);
        }

        Customer::create([
            'title' =>  $request->title,
            'description' => $request->description,
            'image' => $image,
            'banner_tag_id' => $request->banner,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'status' => $request->status == "Draft" ? 0 : 1,
        ]);
        return redirect()->route('customer.index')->with('successMsg', 'Customer is created.');
    }

    public function edit_db(Request $request)
    {
        $this->validate($request, [
            'name' => 'required',
            'phone' => 'required|numeric',
            'address' => 'required',
            'postcode' => 'required|digits:5|numeric',
            'referral_code' => 'required',
        ]);

        $customer = Customer::find($request->customer_id);

        $customer->name = $request->name;
        $customer->phone = $request->phone;
        $customer->address = $request->address;
        $customer->postcode = $request->postcode;
        $customer->referral_code = $request->referral_code;
        $customer->isIndividual = $request->isIndividual;
        $customer->state = $request->state;
        $customer->city = $request->city;
        $customer->save();

        return redirect()->route('customer.index')->with('successMsg', 'Customer is edited.');
    }

    // public function delete($id)
    // {
    //     $customer = Customer::find($id);
    //     File::delete('nucycle-admin/images/customer_image/' . $customer->image);
    //     $customer->delete();

    //     return redirect()->route('customer.index')->with('successMsg', 'Customer is deleted.');
    // }
}
