<?php

namespace Database\Seeders;

use App\Models\SiteSetting;
use Illuminate\Database\Seeder;

class SiteSettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $settings = [
            // General Settings
            [
                'key' => 'site_name',
                'value' => 'غرفة الأخبار',
                'type' => 'text',
                'group' => 'general',
                'description' => 'اسم الموقع',
                'order' => 1
            ],
            [
                'key' => 'site_name_en',
                'value' => 'Newsroom',
                'type' => 'text',
                'group' => 'general',
                'description' => 'اسم الموقع بالإنجليزية',
                'order' => 2
            ],
            [
                'key' => 'site_slogan',
                'value' => 'نبض الشارع - أخبارك من المصدر',
                'type' => 'text',
                'group' => 'general',
                'description' => 'شعار الموقع',
                'order' => 3
            ],
            [
                'key' => 'site_description',
                'value' => 'منصة إخبارية شاملة تقدم أحدث الأخبار والتحليلات من مصادر موثوقة. نسعى لتقديم محتوى إخباري متوازن ودقيق.',
                'type' => 'textarea',
                'group' => 'general',
                'description' => 'وصف الموقع',
                'order' => 4
            ],
            [
                'key' => 'site_keywords',
                'value' => 'أخبار، أخبار عربية، أخبار اليمن، مقالات رأي، تحليلات سياسية، أخبار عدن',
                'type' => 'textarea',
                'group' => 'general',
                'description' => 'الكلمات المفتاحية',
                'order' => 5
            ],
            [
                'key' => 'site_logo',
                'value' => '/logo.png',
                'type' => 'image',
                'group' => 'general',
                'description' => 'شعار الموقع',
                'order' => 6
            ],
            [
                'key' => 'site_logo_width',
                'value' => '180',
                'type' => 'text',
                'group' => 'general',
                'description' => 'عرض الشعار بالبكسل',
                'order' => 7
            ],
            [
                'key' => 'site_favicon',
                'value' => '/favicon.ico',
                'type' => 'image',
                'group' => 'general',
                'description' => 'أيقونة الموقع',
                'order' => 8
            ],

            // SEO Settings
            [
                'key' => 'seo_title_separator',
                'value' => ' - ',
                'type' => 'text',
                'group' => 'seo',
                'description' => 'فاصل العنوان',
                'order' => 1
            ],
            [
                'key' => 'site_locale',
                'value' => 'ar_SA',
                'type' => 'text',
                'group' => 'seo',
                'description' => 'لغة الموقع (ar_SA, en_US, etc.)',
                'order' => 2
            ],
            [
                'key' => 'theme_color',
                'value' => '#D4AF37',
                'type' => 'color',
                'group' => 'seo',
                'description' => 'لون السمة للمتصفح (Theme Color)',
                'order' => 3
            ],
            [
                'key' => 'default_og_image',
                'value' => '/og-image.jpg',
                'type' => 'image',
                'group' => 'seo',
                'description' => 'الصورة الافتراضية لـ Open Graph',
                'order' => 4
            ],
            [
                'key' => 'twitter_handle',
                'value' => '@newsroom',
                'type' => 'text',
                'group' => 'seo',
                'description' => 'حساب تويتر (مثال: @newsroom)',
                'order' => 5
            ],
            [
                'key' => 'seo_google_analytics',
                'value' => '',
                'type' => 'text',
                'group' => 'seo',
                'description' => 'Google Analytics ID',
                'order' => 6
            ],
            [
                'key' => 'seo_google_verification',
                'value' => '',
                'type' => 'text',
                'group' => 'seo',
                'description' => 'Google Site Verification',
                'order' => 7
            ],

            // Organization Info (Schema.org)
            [
                'key' => 'org_founding_date',
                'value' => '2024',
                'type' => 'text',
                'group' => 'organization',
                'description' => 'سنة التأسيس',
                'order' => 1
            ],
            [
                'key' => 'org_area_served',
                'value' => 'اليمن',
                'type' => 'text',
                'group' => 'organization',
                'description' => 'منطقة الخدمة',
                'order' => 2
            ],
            [
                'key' => 'org_address_country',
                'value' => 'YE',
                'type' => 'text',
                'group' => 'organization',
                'description' => 'رمز الدولة (ISO)',
                'order' => 3
            ],
            [
                'key' => 'org_address_city',
                'value' => 'عدن',
                'type' => 'text',
                'group' => 'organization',
                'description' => 'المدينة',
                'order' => 4
            ],

            // Contact Info
            [
                'key' => 'contact_email',
                'value' => 'info@newsroom.com',
                'type' => 'text',
                'group' => 'contact',
                'description' => 'البريد الإلكتروني',
                'order' => 1
            ],
            [
                'key' => 'contact_phone',
                'value' => '+967-xxx-xxx-xxx',
                'type' => 'text',
                'group' => 'contact',
                'description' => 'رقم الهاتف',
                'order' => 2
            ],
            [
                'key' => 'contact_address',
                'value' => 'عدن، اليمن',
                'type' => 'text',
                'group' => 'contact',
                'description' => 'العنوان',
                'order' => 3
            ],

            // Social Media
            [
                'key' => 'social_facebook',
                'value' => 'https://facebook.com/newsroom',
                'type' => 'text',
                'group' => 'social',
                'description' => 'رابط فيسبوك',
                'order' => 1
            ],
            [
                'key' => 'social_twitter',
                'value' => 'https://twitter.com/newsroom',
                'type' => 'text',
                'group' => 'social',
                'description' => 'رابط تويتر (X)',
                'order' => 2
            ],
            [
                'key' => 'social_instagram',
                'value' => 'https://instagram.com/newsroom',
                'type' => 'text',
                'group' => 'social',
                'description' => 'رابط إنستجرام',
                'order' => 3
            ],
            [
                'key' => 'social_youtube',
                'value' => 'https://youtube.com/newsroom',
                'type' => 'text',
                'group' => 'social',
                'description' => 'رابط يوتيوب',
                'order' => 4
            ],
            [
                'key' => 'social_tiktok',
                'value' => '',
                'type' => 'text',
                'group' => 'social',
                'description' => 'رابط تيك توك',
                'order' => 5
            ],
            [
                'key' => 'social_telegram',
                'value' => '',
                'type' => 'text',
                'group' => 'social',
                'description' => 'رابط تليجرام',
                'order' => 6
            ],
        ];

        foreach ($settings as $setting) {
            SiteSetting::updateOrCreate(
                ['key' => $setting['key']],
                $setting
            );
        }

        $this->command->info('Site settings seeded successfully!');
    }
}
