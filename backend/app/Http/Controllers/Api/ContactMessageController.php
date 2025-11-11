<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ContactMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ContactMessageController extends Controller
{
    /**
     * إرسال رسالة جديدة من الفرونت إند
     */
    public function store(Request $request)
    {
        try {
            // التحقق من البيانات
            $validator = Validator::make($request->all(), [
                'full_name' => 'required|string|max:255',
                'email' => 'required|email|max:255',
                'phone' => 'required|string|max:20',
                'meeting_type' => 'required|in:private,general,urgent',
                'subject' => 'required|string|max:255',
                'message' => 'required|string|min:10',
            ], [
                'full_name.required' => 'الاسم الكامل مطلوب',
                'email.required' => 'البريد الإلكتروني مطلوب',
                'email.email' => 'البريد الإلكتروني غير صحيح',
                'phone.required' => 'رقم الهاتف مطلوب',
                'meeting_type.required' => 'نوع اللقاء مطلوب',
                'subject.required' => 'الموضوع مطلوب',
                'message.required' => 'الرسالة مطلوبة',
                'message.min' => 'الرسالة يجب أن تكون 10 أحرف على الأقل',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'خطأ في البيانات المدخلة',
                    'errors' => $validator->errors()
                ], 422);
            }

            // إنشاء الرسالة
            $message = ContactMessage::create($request->all());

            return response()->json([
                'success' => true,
                'message' => 'تم إرسال رسالتك بنجاح. سيتم التواصل معك قريباً.',
                'data' => $message
            ], 201);

        } catch (\Exception $e) {
            \Log::error('Error storing contact message: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء إرسال الرسالة. يرجى المحاولة مرة أخرى.'
            ], 500);
        }
    }
}
