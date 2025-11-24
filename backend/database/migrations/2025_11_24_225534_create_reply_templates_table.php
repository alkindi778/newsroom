<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('reply_templates', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // اسم القالب
            $table->string('subject')->nullable(); // عنوان البريد الافتراضي
            $table->text('content'); // محتوى القالب
            $table->enum('category', ['acknowledgment', 'followup', 'rejection', 'approval', 'general'])->default('general');
            $table->boolean('is_active')->default(true);
            $table->integer('usage_count')->default(0); // عدد مرات الاستخدام
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
            
            $table->index('category');
            $table->index('is_active');
        });
        
        // إدراج قوالب افتراضية
        \DB::table('reply_templates')->insert([
            [
                'name' => 'تأكيد الاستلام',
                'subject' => 'تأكيد استلام رسالتكم',
                'content' => "السيد/ة {name} المحترم/ة،\n\nتحية طيبة وبعد،\n\nنود إعلامكم باستلام رسالتكم بخصوص \"{subject}\"، وسيتم دراستها والرد عليكم في أقرب وقت ممكن.\n\nمع خالص التقدير،\nإدارة المكتب",
                'category' => 'acknowledgment',
                'is_active' => true,
                'usage_count' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'تحويل للجهة المختصة',
                'subject' => 'تحديث بخصوص طلبكم',
                'content' => "السيد/ة {name} المحترم/ة،\n\nتحية طيبة وبعد،\n\nنود إعلامكم بأنه تم تحويل طلبكم إلى الجهة المختصة للنظر فيه. سيتم التواصل معكم فور صدور أي قرار.\n\nمع خالص التقدير،\nإدارة المكتب",
                'category' => 'followup',
                'is_active' => true,
                'usage_count' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'الموافقة على الطلب',
                'subject' => 'الموافقة على طلبكم',
                'content' => "السيد/ة {name} المحترم/ة،\n\nتحية طيبة وبعد،\n\nيسعدنا إبلاغكم بالموافقة على طلبكم بخصوص \"{subject}\". سيتم التواصل معكم لتحديد موعد مناسب.\n\nمع خالص التقدير،\nإدارة المكتب",
                'category' => 'approval',
                'is_active' => true,
                'usage_count' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'الاعتذار عن الطلب',
                'subject' => 'بخصوص طلبكم',
                'content' => "السيد/ة {name} المحترم/ة،\n\nتحية طيبة وبعد،\n\nnشكركم على تواصلكم. نأسف لإبلاغكم بأنه لا يمكننا تلبية طلبكم في الوقت الحالي للأسباب التالية:\n[اذكر السبب]\n\nنتمنى لكم التوفيق.\n\nمع خالص التقدير،\nإدارة المكتب",
                'category' => 'rejection',
                'is_active' => true,
                'usage_count' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reply_templates');
    }
};
