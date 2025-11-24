<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ReplyTemplate;
use Illuminate\Http\Request;

class ReplyTemplateController extends Controller
{
    /**
     * عرض قائمة القوالب
     */
    public function index()
    {
        abort_unless(auth()->user()->canAny(['manage_reply_templates', 'manage_contact_messages']), 403);

        $templates = ReplyTemplate::with('creator')
            ->orderBy('category')
            ->orderBy('usage_count', 'desc')
            ->paginate(20);

        return view('admin.contact-messages.templates.index', compact('templates'));
    }

    /**
     * عرض نموذج الإنشاء
     */
    public function create()
    {
        abort_unless(auth()->user()->canAny(['manage_reply_templates', 'manage_contact_messages']), 403);

        return view('admin.contact-messages.templates.create');
    }

    /**
     * حفظ قالب جديد
     */
    public function store(Request $request)
    {
        abort_unless(auth()->user()->canAny(['manage_reply_templates', 'manage_contact_messages']), 403);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'subject' => 'nullable|string|max:255',
            'content' => 'required|string',
            'category' => 'required|in:acknowledgment,followup,rejection,approval,general',
            'is_active' => 'boolean',
        ]);

        $validated['created_by'] = auth()->id();
        $validated['is_active'] = $request->has('is_active');

        ReplyTemplate::create($validated);

        return redirect()->route('admin.contact-messages.templates.index')
            ->with('success', 'تم إنشاء القالب بنجاح');
    }

    /**
     * عرض نموذج التعديل
     */
    public function edit($id)
    {
        abort_unless(auth()->user()->canAny(['manage_reply_templates', 'manage_contact_messages']), 403);

        $template = ReplyTemplate::findOrFail($id);

        return view('admin.contact-messages.templates.edit', compact('template'));
    }

    /**
     * تحديث القالب
     */
    public function update(Request $request, $id)
    {
        abort_unless(auth()->user()->canAny(['manage_reply_templates', 'manage_contact_messages']), 403);

        $template = ReplyTemplate::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'subject' => 'nullable|string|max:255',
            'content' => 'required|string',
            'category' => 'required|in:acknowledgment,followup,rejection,approval,general',
            'is_active' => 'boolean',
        ]);

        $validated['is_active'] = $request->has('is_active');

        $template->update($validated);

        return redirect()->route('admin.contact-messages.templates.index')
            ->with('success', 'تم تحديث القالب بنجاح');
    }

    /**
     * حذف القالب
     */
    public function destroy($id)
    {
        abort_unless(auth()->user()->canAny(['manage_reply_templates', 'manage_contact_messages']), 403);

        $template = ReplyTemplate::findOrFail($id);
        $template->delete();

        return redirect()->route('admin.contact-messages.templates.index')
            ->with('success', 'تم حذف القالب بنجاح');
    }

    /**
     * تفعيل/تعطيل القالب
     */
    public function toggle($id)
    {
        abort_unless(auth()->user()->canAny(['manage_reply_templates', 'manage_contact_messages']), 403);

        $template = ReplyTemplate::findOrFail($id);
        $template->update(['is_active' => !$template->is_active]);

        $status = $template->is_active ? 'تفعيل' : 'تعطيل';
        return redirect()->route('admin.contact-messages.templates.index')
            ->with('success', "تم {$status} القالب بنجاح");
    }
}
