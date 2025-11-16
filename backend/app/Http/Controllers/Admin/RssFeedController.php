<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\RssFeed;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class RssFeedController extends Controller
{
    public function index()
    {
        $feeds = RssFeed::with('category')
            ->latest()
            ->paginate(15);
            
        return view('admin.rss.index', compact('feeds'));
    }

    public function create()
    {
        $categories = Category::orderBy('name')->get();
        return view('admin.rss.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'category_id' => 'nullable|exists:categories,id',
            'language' => 'required|in:ar,en',
            'items_count' => 'required|integer|min:1|max:100',
            'ttl' => 'required|integer|min:1',
            'copyright' => 'nullable|string|max:255',
            'image_url' => 'nullable|url',
            'is_active' => 'boolean',
        ]);

        $validated['slug'] = Str::slug($validated['title']);
        $validated['is_active'] = $request->has('is_active');

        RssFeed::create($validated);

        return redirect()
            ->route('admin.rss.index')
            ->with('success', 'تم إضافة التغذية بنجاح');
    }

    public function edit(RssFeed $rssFeed)
    {
        $categories = Category::orderBy('name')->get();
        return view('admin.rss.edit', compact('rssFeed', 'categories'));
    }

    public function update(Request $request, RssFeed $rssFeed)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'category_id' => 'nullable|exists:categories,id',
            'language' => 'required|in:ar,en',
            'items_count' => 'required|integer|min:1|max:100',
            'ttl' => 'required|integer|min:1',
            'copyright' => 'nullable|string|max:255',
            'image_url' => 'nullable|url',
            'is_active' => 'boolean',
        ]);

        $validated['slug'] = Str::slug($validated['title']);
        $validated['is_active'] = $request->has('is_active');

        $rssFeed->update($validated);

        return redirect()
            ->route('admin.rss.index')
            ->with('success', 'تم تحديث التغذية بنجاح');
    }

    public function destroy(RssFeed $rssFeed)
    {
        $rssFeed->delete();

        return redirect()
            ->route('admin.rss.index')
            ->with('success', 'تم حذف التغذية بنجاح');
    }
}
