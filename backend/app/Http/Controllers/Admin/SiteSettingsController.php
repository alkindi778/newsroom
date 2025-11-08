<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SiteSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SiteSettingsController extends Controller
{
    /**
     * Display settings page
     */
    public function index()
    {
        // Get all settings grouped by group, then transform to key-value pairs
        $settingsCollection = SiteSetting::orderBy('group')->orderBy('order')->get()->groupBy('group');
        
        // Transform each group to key-value array
        $settings = [];
        foreach ($settingsCollection as $group => $groupSettings) {
            $settings[$group] = $groupSettings->pluck('value', 'key')->toArray();
        }
        
        \Log::info('Settings loaded for display', [
            'groups' => array_keys($settings),
            'general_keys' => isset($settings['general']) ? array_keys($settings['general']) : []
        ]);
        
        return view('admin.settings.index', compact('settings'));
    }

    /**
     * Show single setting
     */
    public function show(string $key)
    {
        $setting = SiteSetting::where('key', $key)->firstOrFail();
        
        return view('admin.settings.show', compact('setting'));
    }

    /**
     * Store new setting
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'key' => 'required|string|unique:site_settings,key',
            'value' => 'required',
            'type' => 'required|string|in:text,textarea,image,boolean,json',
            'group' => 'required|string',
            'description' => 'nullable|string',
            'order' => 'nullable|integer'
        ]);

        // Handle image upload
        if ($validated['type'] === 'image' && $request->hasFile('value')) {
            $path = $request->file('value')->store('settings', 'public');
            $validated['value'] = $path;
        }

        $setting = SiteSetting::create($validated);

        return redirect()->route('admin.settings.index')
            ->with('success', 'تم إضافة الإعداد بنجاح');
    }

    /**
     * Update setting
     */
    public function update(Request $request, string $key)
    {
        $setting = SiteSetting::where('key', $key)->firstOrFail();

        $validated = $request->validate([
            'value' => 'required',
            'description' => 'nullable|string',
            'order' => 'nullable|integer'
        ]);

        // Handle image upload
        if ($setting->type === 'image' && $request->hasFile('value')) {
            // Delete old image
            if ($setting->value && Storage::disk('public')->exists($setting->value)) {
                Storage::disk('public')->delete($setting->value);
            }
            
            $path = $request->file('value')->store('settings', 'public');
            $validated['value'] = $path;
        }

        $setting->update($validated);

        return redirect()->route('admin.settings.index')
            ->with('success', 'تم تحديث الإعداد بنجاح');
    }

    /**
     * Bulk update settings
     */
    public function bulkUpdate(Request $request)
    {
        try {
            $settings = $request->input('settings', []);
            $files = $request->file('settings', []);
            
            \Log::info('Settings Update Request', [
                'settings_keys' => array_keys($settings),
                'files_keys' => array_keys($files)
            ]);

            // Process text settings first
            foreach ($settings as $key => $value) {
                $setting = SiteSetting::where('key', $key)->first();
                
                if (!$setting) {
                    // Create new setting if it doesn't exist
                    $setting = new SiteSetting();
                    $setting->key = $key;
                    $setting->type = 'text'; // Default type
                    $setting->group = 'general'; // Default group
                    
                    \Log::info("Creating new setting: {$key}");
                }
                
                // Skip if this is an image setting that has a file upload
                if ($setting->type === 'image' && isset($files[$key])) {
                    continue; // Will be processed in files loop below
                }
                
                $setting->value = $value;
                $setting->save();
                
                \Log::info("Updated setting: {$key} = {$value}");
            }
            
            // Process image uploads separately
            foreach ($files as $key => $file) {
                if ($file && $file->isValid()) {
                    $setting = SiteSetting::where('key', $key)->first();
                    
                    if (!$setting) {
                        // Create new image setting
                        $setting = new SiteSetting();
                        $setting->key = $key;
                        $setting->type = 'image';
                        $setting->group = 'general';
                        
                        \Log::info("Creating new image setting: {$key}");
                    }
                    
                    \Log::info("Processing image upload for: {$key}");
                    
                    // Delete old image
                    if ($setting->value && Storage::disk('public')->exists($setting->value)) {
                        Storage::disk('public')->delete($setting->value);
                        \Log::info("Deleted old image: {$setting->value}");
                    }
                    
                    $path = $file->store('settings', 'public');
                    $setting->value = $path;
                    $setting->save();
                    
                    \Log::info("Uploaded new image for {$key}: {$path}");
                }
            }

            return redirect()->route('admin.settings.index')
                ->with('success', 'تم تحديث الإعدادات بنجاح');
                
        } catch (\Exception $e) {
            \Log::error('Settings Update Error', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return redirect()->route('admin.settings.index')
                ->with('error', 'حدث خطأ أثناء تحديث الإعدادات: ' . $e->getMessage());
        }
    }

    /**
     * Delete setting
     */
    public function destroy(string $key)
    {
        $setting = SiteSetting::where('key', $key)->firstOrFail();

        // Delete image if exists
        if ($setting->type === 'image' && $setting->value && Storage::disk('public')->exists($setting->value)) {
            Storage::disk('public')->delete($setting->value);
        }

        $setting->delete();

        return redirect()->route('admin.settings.index')
            ->with('success', 'تم حذف الإعداد بنجاح');
    }
}
