<?php

namespace App\Http\Controllers;

use App\Models\ContactUsInfo;
use App\Models\ContactUsEnquiry;
use Illuminate\Http\Request;

class ContactUsController extends Controller
{
    public function index()
    {
        return view('contact_us.index');
    }

    public function content_index()
    {
        $content = ContactUsInfo::first();
        return view('contact_us.content.index', compact('content'));
    }

    public function content_view($id)
    {
        $content = ContactUsInfo::find($id);
        return view('contact_us.content.view', compact('content'));
    }

    public function content_edit()
    {
        $content = ContactUsInfo::first();
        $id = $content->id; 
        return view('contact_us.content.edit', compact('content', 'id'));
    }

    public function content_edit_db(Request $request)
    {   
        $this->validate($request, [
            'fb' => 'required',
            'ig' => 'required',
            'web' => 'required',
            'phone' => 'required',
            'email' => 'required',
            'address' => 'required',
        ]);

        $content = ContactUsInfo::first();
        $content->facebook_url = $request->fb;
        $content->instagram_url = $request->ig;
        $content->website_url = $request->web;
        $content->phone = $request->phone;
        $content->email = $request->email;
        $content->address = $request->address;
        $content->save();

        return redirect()->route('contact_us.content.index')->with('successMsg', 'Content is edited.');
    }

    public function enquiry_index()
    {
        $enquiries = ContactUsEnquiry::get();
        return view('contact_us.enquiry.index', compact('enquiries'));
    }

    public function enquiry_view($id)
    {
        $enquiry = ContactUsEnquiry::find($id);
        return view('contact_us.enquiry.view', compact('enquiry'));
    }
}
