<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SocialMediaPost;
use App\Models\Article;
use App\Services\SocialMediaService;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class SocialMediaController extends Controller
{
    public function __construct(private SocialMediaService $socialMediaService)
    {
    }

    /**
     * عرض إعدادات التواصل الاجتماعي
     */
    public function settings(): View
    {
        $config = config('social-media');
        $platforms = $config['platforms'];

        return view('admin.social-media.settings', compact('platforms'));
    }

    /**
     * تحديث إعدادات المنصة
     */
    public function updateSettings(Request $request): RedirectResponse
    {
        $platform = $request->input('platform');
        $settings = $request->validate([
            'enabled' => 'boolean',
            'auto_publish' => 'boolean',
            'include_image' => 'boolean',
            'include_link' => 'boolean',
            'access_token' => 'nullable|string',
            'api_key' => 'nullable|string',
            'api_secret' => 'nullable|string',
            'bot_token' => 'nullable|string',
            'channel_id' => 'nullable|string',
            'page_id' => 'nullable|string',
        ]);

        // حفظ الإعدادات في .env
        $this->updateEnv($platform, $settings);

        return back()->with('success', "تم تحديث إعدادات {$platform} بنجاح");
    }

    /**
     * عرض المنشورات
     */
    public function posts(Request $request): View
    {
        $query = SocialMediaPost::with('article');

        if ($request->has('platform') && $request->input('platform')) {
            $query->where('platform', $request->input('platform'));
        }

        if ($request->has('status') && $request->input('status')) {
            $query->where('status', $request->input('status'));
        }

        $posts = $query->latest()->paginate(20);

        return view('admin.social-media.posts', compact('posts'));
    }

    /**
     * نشر مقالة يدويًا
     */
    public function publishArticle(Request $request, Article $article): RedirectResponse
    {
        try {
            $results = $this->socialMediaService->publishArticle($article);

            $successCount = collect($results)->filter(fn($r) => $r['success'] ?? false)->count();
            $failCount = collect($results)->filter(fn($r) => !($r['success'] ?? false))->count();

            $message = "تم النشر على {$successCount} منصات";
            if ($failCount > 0) {
                $message .= " وفشل النشر على {$failCount} منصات";
            }

            return back()->with('success', $message);
        } catch (\Exception $e) {
            return back()->with('error', 'حدث خطأ: ' . $e->getMessage());
        }
    }

    /**
     * إعادة محاولة نشر فاشل
     */
    public function retryPost(SocialMediaPost $post): RedirectResponse
    {
        try {
            $article = $post->article;
            $result = $this->socialMediaService->publishArticle($article);

            if ($result[$post->platform]['success'] ?? false) {
                $post->update([
                    'status' => 'published',
                    'published_at' => now(),
                    'error_message' => null,
                ]);

                return back()->with('success', 'تم إعادة النشر بنجاح');
            }
        } catch (\Exception $e) {
            return back()->with('error', 'فشلت إعادة المحاولة: ' . $e->getMessage());
        }

        return back()->with('error', 'حدث خطأ غير متوقع');
    }

    /**
     * حذف منشور
     */
    public function deletePost(SocialMediaPost $post): RedirectResponse
    {
        $post->delete();

        return back()->with('success', 'تم حذف المنشور');
    }

    /**
     * تحديث ملف .env
     */
    private function updateEnv(string $platform, array $settings): void
    {
        $envPath = base_path('.env');
        $envContent = file_get_contents($envPath);

        $platformUpper = strtoupper($platform);

        // تحديث أو إضافة المتغيرات
        foreach ($settings as $key => $value) {
            $envKey = "{$platformUpper}_" . strtoupper($key);
            $pattern = "/^{$envKey}=.*/m";

            if (preg_match($pattern, $envContent)) {
                $envContent = preg_replace($pattern, "{$envKey}={$value}", $envContent);
            } else {
                $envContent .= "\n{$envKey}={$value}";
            }
        }

        file_put_contents($envPath, $envContent);
    }
}
