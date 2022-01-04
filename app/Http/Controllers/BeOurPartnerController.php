<?php

namespace App\Http\Controllers;

use App\Models\BeOurPartnerContent;
use App\Models\BeOurPartnerEnquiry;
use Illuminate\Http\Request;

class BeOurPartnerController extends Controller
{
    public function index()
    {
        return view('be_our_partner.index');
    }

    public function content_index()
    {
        $contents = BeOurPartnerContent::get();
        return view('be_our_partner.content.index', compact('contents'));
    }

    public function content_view($id)
    {
        $content = BeOurPartnerContent::find($id);
        return view('be_our_partner.content.view', compact('content'));
    }

    public function content_edit($id)
    {
        $content = BeOurPartnerContent::find($id);
        return view('be_our_partner.content.edit', compact('content', 'id'));
    }

    public function content_edit_db(Request $request)
    {
        $this->validate($request, [
            'content' => 'required',
        ]);

        $content = BeOurPartnerContent::find($request->content_id);
        $content->content = $request->content;
        $content->save();

        return redirect()->route('be_our_partner.content.index')->with('successMsg', 'Content is edited.');
    }

    public function enquiry_index()
    {
        $enquiries = BeOurPartnerEnquiry::get();
        return view('be_our_partner.enquiry.index', compact('enquiries'));
    }

    public function enquiry_view($id)
    {
        $enquiry = BeOurPartnerEnquiry::find($id);
        return view('be_our_partner.enquiry.view', compact('enquiry'));
    }
}
