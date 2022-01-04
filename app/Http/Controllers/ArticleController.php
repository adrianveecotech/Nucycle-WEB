<?php

namespace App\Http\Controllers;

use App\Models\BannerTag;
use App\Models\Article;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class ArticleController extends Controller
{
    public function index(Request $request)
    {
        $articles = Article::get();
        return view('article.index', compact('articles'));
    }

    public function create()
    {
        $banners = BannerTag::get();
        return view('article.create', compact('banners'));
    }

    public function edit($id)
    {
        $article = Article::find($id);
        $banners = BannerTag::get();
        return view('article.edit', compact('article', 'banners', 'id'));
    }

    public function view($id)
    {
        $article = Article::find($id);
        $banners = BannerTag::get();
        return view('article.view', compact('article', 'banners', 'id'));
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
            $file->move('nucycle-admin/images/article_image', $image);
        }

        Article::create([
            'title' =>  $request->title,
            'description' => $request->description,
            'image' => $image,
            'banner_tag_id' => $request->banner,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'status' => $request->status == "Draft" ? 0 : 1,
        ]);

        return redirect()->route('article.index')->with('successMsg', 'Article is created.');
    }

    public function edit_db(Request $request)
    {
        $this->validate($request, [
            'title' => 'required',
            'description' => 'required',
            'start_date' => 'required',
            'end_date' => 'required',
        ]);

        $article = Article::find($request->article_id);

        $image = '';
        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $image = date('YmdHis') . "." . $file->getClientOriginalExtension();
            $file->move('nucycle-admin/images/article_image', $image);
            File::delete('nucycle-admin/images/article_image/' . $article->image);
        } else {
            $image = $article->image;
        }

        $article->title = $request->title;
        $article->description = $request->description;
        $article->image = $image;
        $article->banner_tag_id = $request->banner;
        $article->start_date = $request->start_date;
        $article->end_date = $request->end_date;
        $article->status = $request->status == "Draft" ? 0 : 1;
        $article->save();

        return redirect()->route('article.index')->with('successMsg', 'Article is edited.');
    }

    public function delete($id)
    {
        $article = Article::find($id);
        File::delete('nucycle-admin/images/article_image/' . $article->image);
        $article->delete();
        return redirect()->route('article.index')->with('successMsg', 'Article is deleted.');
    }
}
