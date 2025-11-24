<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ContactMessage;
use App\Models\ContactMessageReply;
use App\Models\ReplyTemplate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class ContactMessageReplyController extends Controller
{
    /**
     * إرسال رد عبر البريد الإلكتروني
     */
    public function sendEmail(Request $request, $messageId)
    {
        abort_unless(auth()->user()->can('manage_contact_messages'), 403);

        $request->validate([
            'subject' => 'required|string|max:255',
            'content' => 'required|string',
            'template_id' => 'nullable|exists:reply_templates,id',
        ]);

        $message = ContactMessage::findOrFail($messageId);

        try {
            // الحصول على اسم الموقع من الإعدادات
            $siteName = \App\Models\SiteSetting::where('key', 'site_name')->value('value') ?? config('app.name');
            $fromEmail = config('mail.from.address');
            
            // إرسال البريد
            Mail::send([], [], function ($mail) use ($message, $request, $siteName, $fromEmail) {
                $mail->from($fromEmail, $siteName)
                    ->to($message->email, $message->full_name)
                    ->subject($request->subject)
                    ->html($this->formatEmailContent($request->content, $message));
            });

            // حفظ الرد في قاعدة البيانات
            $reply = ContactMessageReply::create([
                'contact_message_id' => $message->id,
                'user_id' => auth()->id(),
                'type' => 'email',
                'subject' => $request->subject,
                'content' => $request->content,
                'sent_successfully' => true,
                'sent_at' => now(),
            ]);

            // تحديث وقت أول رد إذا لم يكن موجوداً
            if (!$message->first_reply_at) {
                $message->update([
                    'first_reply_at' => now(),
                    'status' => 'in_progress',
                ]);
            }

            // تحديث عدد الردود
            $message->increment('replies_count');

            // تحديث عدد استخدام القالب
            if ($request->template_id) {
                ReplyTemplate::find($request->template_id)?->incrementUsage();
            }

            return redirect()->route('admin.contact-messages.show', $message->id)
                ->with('success', 'تم إرسال الرد بنجاح إلى ' . $message->email);

        } catch (\Exception $e) {
            Log::error('Failed to send email reply', [
                'message_id' => $messageId,
                'error' => $e->getMessage()
            ]);

            // حفظ الرد كفاشل
            ContactMessageReply::create([
                'contact_message_id' => $message->id,
                'user_id' => auth()->id(),
                'type' => 'email',
                'subject' => $request->subject,
                'content' => $request->content,
                'sent_successfully' => false,
            ]);

            return redirect()->route('admin.contact-messages.show', $message->id)
                ->with('error', 'فشل إرسال البريد: ' . $e->getMessage());
        }
    }

    /**
     * إضافة ملاحظة داخلية
     */
    public function addNote(Request $request, $messageId)
    {
        abort_unless(auth()->user()->can('manage_contact_messages'), 403);

        $request->validate([
            'content' => 'required|string',
        ]);

        $message = ContactMessage::findOrFail($messageId);

        ContactMessageReply::create([
            'contact_message_id' => $message->id,
            'user_id' => auth()->id(),
            'type' => 'internal_note',
            'content' => $request->content,
        ]);

        return redirect()->route('admin.contact-messages.show', $message->id)
            ->with('success', 'تم إضافة الملاحظة بنجاح');
    }

    /**
     * حذف رد/ملاحظة
     */
    public function destroy($messageId, $replyId)
    {
        abort_unless(auth()->user()->can('manage_contact_messages'), 403);

        $reply = ContactMessageReply::where('contact_message_id', $messageId)
            ->findOrFail($replyId);

        // فقط صاحب الرد أو الأدمن يمكنه الحذف
        if ($reply->user_id !== auth()->id() && !auth()->user()->hasRole('super-admin')) {
            abort(403, 'لا يمكنك حذف هذا الرد');
        }

        $reply->delete();

        // تحديث عدد الردود
        $reply->contactMessage->decrement('replies_count');

        return redirect()->route('admin.contact-messages.show', $messageId)
            ->with('success', 'تم حذف الرد بنجاح');
    }

    /**
     * الحصول على قوالب الردود (API)
     */
    public function getTemplates(Request $request)
    {
        $templates = ReplyTemplate::active()
            ->when($request->category, function ($q, $category) {
                $q->byCategory($category);
            })
            ->orderBy('usage_count', 'desc')
            ->get();

        return response()->json($templates);
    }

    /**
     * الحصول على قالب محدد مع استبدال المتغيرات (API)
     */
    public function getTemplate($templateId, $messageId)
    {
        $template = ReplyTemplate::findOrFail($templateId);
        $message = ContactMessage::findOrFail($messageId);

        return response()->json([
            'subject' => $template->parseSubject($message),
            'content' => $template->parseContent($message),
        ]);
    }

    /**
     * معاينة قالب البريد الإلكتروني
     */
    public function previewEmail($messageId)
    {
        abort_unless(auth()->user()->can('manage_contact_messages'), 403);

        $message = ContactMessage::findOrFail($messageId);
        
        // نص تجريبي للمعاينة
        $sampleContent = "شكراً لتواصلكم معنا.\n\nنود إعلامكم بأننا استلمنا رسالتكم وسنقوم بالرد عليها في أقرب وقت ممكن.\n\nفي حال كان لديكم أي استفسارات إضافية، لا تترددوا في التواصل معنا.";
        
        $emailHtml = $this->formatEmailContent($sampleContent, $message);
        
        return view('admin.contact-messages.email-preview', compact('emailHtml', 'message'));
    }

    /**
     * تنسيق محتوى البريد باستخدام قالب احترافي
     */
    private function formatEmailContent($content, $message)
    {
        // الحصول على إعدادات الموقع
        $settings = \App\Models\SiteSetting::all()->pluck('value', 'key');
        
        // بناء رابط الشعار
        $logoPath = $settings['site_logo'] ?? null;
        $logoUrl = $logoPath ? url('storage/' . $logoPath) : null;
        
        // بيانات القالب
        $data = [
            'subject' => $message->subject,
            'content' => $content,
            'recipientName' => $message->full_name,
            'originalSubject' => $message->subject,
            'siteName' => $settings['site_name'] ?? config('app.name'),
            'siteSlogan' => $settings['site_slogan'] ?? null,
            'logoUrl' => $logoUrl,
            'themeColor' => $settings['theme_color'] ?? '#d4af37',
            'contactEmail' => $settings['contact_email'] ?? null,
            'contactPhone' => $settings['contact_phone'] ?? null,
            'contactAddress' => $settings['contact_address'] ?? null,
            'socialLinks' => [
                'facebook' => $settings['social_facebook'] ?? null,
                'twitter' => $settings['social_twitter'] ?? null,
                'instagram' => $settings['social_instagram'] ?? null,
                'youtube' => $settings['social_youtube'] ?? null,
            ],
        ];
        
        return view('emails.contact-reply', $data)->render();
    }
}
