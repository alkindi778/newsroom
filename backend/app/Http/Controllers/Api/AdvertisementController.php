<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\AdvertisementService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AdvertisementController extends Controller
{
    protected $advertisementService;

    public function __construct(AdvertisementService $advertisementService)
    {
        $this->advertisementService = $advertisementService;
    }

    /**
     * Get advertisements for specific position
     */
    public function getByPosition(Request $request, $position)
    {
        try {
            $device = $request->get('device');
            $advertisements = $this->advertisementService->getByPosition($position);

            // Filter by device if specified
            if ($device) {
                $advertisements = $advertisements->filter(function ($ad) use ($device) {
                    return empty($ad->target_devices) || in_array($device, $ad->target_devices);
                });
            }

            return response()->json([
                'success' => true,
                'data' => $advertisements->map(function ($ad) {
                    return [
                        'id' => $ad->id,
                        'title' => $ad->title,
                        'type' => $ad->type,
                        'position' => $ad->position,
                        'layout' => $ad->layout ?? 'single',
                        'image_url' => $ad->image_url,
                        'link' => $ad->link,
                        'open_new_tab' => $ad->open_new_tab,
                        'width' => $ad->width,
                        'height' => $ad->height,
                        'content' => $ad->content,
                    ];
                })->values()
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء جلب الإعلانات'
            ], 500);
        }
    }

    /**
     * Get advertisements for specific page
     */
    public function getForPage(Request $request, $page)
    {
        try {
            $device = $request->get('device');
            $advertisements = $this->advertisementService->getForPage($page, $device);

            return response()->json([
                'success' => true,
                'data' => $advertisements->map(function ($ad) {
                    return [
                        'id' => $ad->id,
                        'title' => $ad->title,
                        'type' => $ad->type,
                        'position' => $ad->position,
                        'layout' => $ad->layout ?? 'single',
                        'image_url' => $ad->image_url,
                        'link' => $ad->link,
                        'open_new_tab' => $ad->open_new_tab,
                        'width' => $ad->width,
                        'height' => $ad->height,
                        'content' => $ad->content,
                    ];
                })->values()
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء جلب الإعلانات'
            ], 500);
        }
    }

    /**
     * Get advertisements after specific section
     */
    public function getAfterSection(Request $request, $sectionId)
    {
        try {
            $page = $request->get('page', 'home');
            $advertisements = $this->advertisementService->getAfterSection($sectionId, $page);

            return response()->json([
                'success' => true,
                'data' => $advertisements->map(function ($ad) {
                    return [
                        'id' => $ad->id,
                        'title' => $ad->title,
                        'type' => $ad->type,
                        'position' => $ad->position,
                        'layout' => $ad->layout ?? 'single',
                        'image_url' => $ad->image_url,
                        'link' => $ad->link,
                        'open_new_tab' => $ad->open_new_tab,
                        'width' => $ad->width,
                        'height' => $ad->height,
                        'content' => $ad->content,
                    ];
                })->values()
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء جلب الإعلانات'
            ], 500);
        }
    }

    /**
     * Track view
     */
    public function trackView(Request $request, $id)
    {
        $validator = Validator::make(['id' => $id], [
            'id' => 'required|integer|exists:advertisements,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'معرف الإعلان غير صحيح'
            ], 422);
        }

        try {
            $this->advertisementService->trackView($id);

            return response()->json([
                'success' => true,
                'message' => 'تم تسجيل المشاهدة بنجاح'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء تسجيل المشاهدة'
            ], 500);
        }
    }

    /**
     * Track click
     */
    public function trackClick(Request $request, $id)
    {
        $validator = Validator::make(['id' => $id], [
            'id' => 'required|integer|exists:advertisements,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'معرف الإعلان غير صحيح'
            ], 422);
        }

        try {
            $this->advertisementService->trackClick($id);

            return response()->json([
                'success' => true,
                'message' => 'تم تسجيل النقرة بنجاح'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء تسجيل النقرة'
            ], 500);
        }
    }
}
