<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Infographic;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class InfographicController extends Controller
{
    /**
     * عرض قائمة الإنفوجرافيكات
     */
    public function index()
    {
        $infographics = Infographic::with('category')
            ->latest()
            ->paginate(20);

        return view('admin.infographics.index', compact('infographics'));
    }

    /**
     * عرض صفحة إنشاء إنفوجرافيك جديد
     */
    public function create()
    {
        $categories = Category::all();
        return view('admin.infographics.create', compact('categories'));
    }

    /**
     * حفظ إنفوجرافيك جديد
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'title_en' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'description_en' => 'nullable|string',
            'image' => 'required|image|mimes:jpeg,jpg,png,gif,webp|max:5120',
            'slug' => 'nullable|string|unique:infographics,slug',
            'category_id' => 'nullable|exists:categories,id',
            'is_featured' => 'boolean',
            'is_active' => 'boolean',
            'order' => 'nullable|integer|min:0',
            'tags' => 'nullable|string',
        ]);

        // رفع الصورة
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imagePath = $image->store('infographics', 'public');
            $validated['image'] = $imagePath;
        }

        // توليد slug إذا لم يكن موجوداً
        if (empty($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['title']);
        }

        // معالجة التاجز
        if (!empty($validated['tags'])) {
            $validated['tags'] = array_map('trim', explode(',', $validated['tags']));
        }

        $validated['is_featured'] = $request->has('is_featured');
        $validated['is_active'] = $request->has('is_active');

        Infographic::create($validated);

        return redirect()
            ->route('admin.infographics.index')
            ->with('success', 'تم إضافة الإنفوجرافيك بنجاح');
    }

    /**
     * عرض صفحة تعديل إنفوجرافيك
     */
    public function edit(Infographic $infographic)
    {
        $categories = Category::all();
        return view('admin.infographics.edit', compact('infographic', 'categories'));
    }

    /**
     * تحديث إنفوجرافيك
     */
    public function update(Request $request, Infographic $infographic)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'title_en' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'description_en' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,jpg,png,gif,webp|max:5120',
            'slug' => 'nullable|string|unique:infographics,slug,' . $infographic->id,
            'category_id' => 'nullable|exists:categories,id',
            'is_featured' => 'boolean',
            'is_active' => 'boolean',
            'order' => 'nullable|integer|min:0',
            'tags' => 'nullable|string',
        ]);

        // رفع صورة جديدة إذا كانت موجودة
        if ($request->hasFile('image')) {
            // حذف الصورة القديمة
            if ($infographic->image && Storage::disk('public')->exists($infographic->image)) {
                Storage::disk('public')->delete($infographic->image);
            }

            $image = $request->file('image');
            $imagePath = $image->store('infographics', 'public');
            $validated['image'] = $imagePath;
        }

        // توليد slug إذا لم يكن موجوداً
        if (empty($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['title']);
        }

        // معالجة التاجز
        if (!empty($validated['tags'])) {
            $validated['tags'] = array_map('trim', explode(',', $validated['tags']));
        }

        $validated['is_featured'] = $request->has('is_featured');
        $validated['is_active'] = $request->has('is_active');

        $infographic->update($validated);

        return redirect()
            ->route('admin.infographics.index')
            ->with('success', 'تم تحديث الإنفوجرافيك بنجاح');
    }

    /**
     * حذف إنفوجرافيك
     */
    public function destroy(Infographic $infographic)
    {
        // حذف الصورة
        if ($infographic->image && Storage::disk('public')->exists($infographic->image)) {
            Storage::disk('public')->delete($infographic->image);
        }

        $infographic->delete();

        return redirect()
            ->route('admin.infographics.index')
            ->with('success', 'تم حذف الإنفوجرافيك بنجاح');
    }

    /**
     * تغيير حالة التفعيل
     */
    public function toggleStatus(Infographic $infographic)
    {
        $infographic->update([
            'is_active' => !$infographic->is_active
        ]);

        $status = $infographic->is_active ? 'تفعيل' : 'تعطيل';

        return redirect()
            ->route('admin.infographics.index')
            ->with('success', "تم {$status} الإنفوجرافيك بنجاح");
    }

    /**
     * تغيير حالة التميز
     */
    public function toggleFeatured(Infographic $infographic)
    {
        $infographic->update([
            'is_featured' => !$infographic->is_featured
        ]);

        $status = $infographic->is_featured ? 'إضافة إلى' : 'إزالة من';

        return redirect()
            ->route('admin.infographics.index')
            ->with('success', "تم {$status} المميزة بنجاح");
    }
}
