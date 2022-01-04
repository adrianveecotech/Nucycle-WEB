<?php

namespace App\Http\Controllers;

use App\Models\BannerTag;
use App\Models\Faq;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class FaqController extends Controller
{
    public function index(Request $request)
    {
        $faqs = Faq::get();
        return view('faq.index', compact('faqs'));
    }

    public function create()
    {
        return view('faq.create');
    }

    public function edit($id)
    {
        $faq = Faq::find($id);
        return view('faq.edit', compact('faq', 'id'));
    }

    public function view($id)
    {
        $faq = Faq::find($id);
        return view('faq.view', compact('faq', 'id'));
    }

    public function insert(Request $request)
    {
        $this->validate($request, [
            'question' => 'required',
            'answer' => 'required',
        ]);
        Faq::create([
            'question' =>  $request->question,
            'answer' => $request->answer,
        ]);

        return redirect()->route('faq.index')->with('successMsg', 'Faq is created.');
    }

    public function edit_db(Request $request)
    {
        $this->validate($request, [
            'question' => 'required',
            'answer' => 'required',
        ]);

        $faq = Faq::find($request->faq_id);
        $faq->question = $request->question;
        $faq->answer = $request->answer;
        $faq->save();

        return redirect()->route('faq.index')->with('successMsg', 'Faq is edited.');
    }

    public function delete($id)
    {
        $faq = Faq::find($id);
        $faq->delete();
        return redirect()->route('faq.index')->with('successMsg', 'Faq is deleted.');
    }
}
