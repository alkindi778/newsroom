<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\SiteSetting;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SiteSettingsController extends Controller
{
    /**
     * Get all settings (public)
     */
    public function index(): JsonResponse
    {
        try {
            $settings = SiteSetting::orderBy('group')->orderBy('order')->get();
            
            // تنظيم الإعدادات حسب المجموعة
            $grouped = $settings->groupBy('group')->map(function ($group) {
                return $group->pluck('value', 'key');
            });

            // إضافة all settings as flat array
            $flat = $settings->pluck('value', 'key');

            return response()->json([
                'success' => true,
                'data' => [
                    'grouped' => $grouped,
                    'flat' => $flat
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ في جلب الإعدادات',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get settings by group (public)
     */
    public function getByGroup(string $group): JsonResponse
    {
        try {
            $settings = SiteSetting::where('group', $group)
                ->orderBy('order')
                ->get()
                ->pluck('value', 'key');

            return response()->json([
                'success' => true,
                'data' => $settings
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ في جلب الإعدادات',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get single setting by key (public)
     */
    public function getByKey(string $key): JsonResponse
    {
        try {
            $setting = SiteSetting::where('key', $key)->first();

            if (!$setting) {
                return response()->json([
                    'success' => false,
                    'message' => 'الإعداد غير موجود'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => $setting->value
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ في جلب الإعداد',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update or create setting (admin only)
     */
    public function update(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'key' => 'required|string',
                'value' => 'required',
                'type' => 'nullable|string|in:text,textarea,image,boolean,json',
                'group' => 'nullable|string',
                'description' => 'nullable|string',
                'order' => 'nullable|integer'
            ]);

            $setting = SiteSetting::updateOrCreate(
                ['key' => $validated['key']],
                $validated
            );

            return response()->json([
                'success' => true,
                'message' => 'تم تحديث الإعداد بنجاح',
                'data' => $setting
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ في تحديث الإعداد',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Bulk update settings (admin only)
     */
    public function bulkUpdate(Request $request): JsonResponse
    {
        try {
            $settings = $request->input('settings', []);

            foreach ($settings as $key => $value) {
                SiteSetting::set($key, $value);
            }

            return response()->json([
                'success' => true,
                'message' => 'تم تحديث الإعدادات بنجاح'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ في تحديث الإعدادات',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete setting (admin only)
     */
    public function destroy(string $key): JsonResponse
    {
        try {
            $setting = SiteSetting::where('key', $key)->first();

            if (!$setting) {
                return response()->json([
                    'success' => false,
                    'message' => 'الإعداد غير موجود'
                ], 404);
            }

            $setting->delete();

            return response()->json([
                'success' => true,
                'message' => 'تم حذف الإعداد بنجاح'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ في حذف الإعداد',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
