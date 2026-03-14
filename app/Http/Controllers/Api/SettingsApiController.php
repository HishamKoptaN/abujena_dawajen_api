<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Models\Settings;
use App\Http\Resources\SettingsResource;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class SettingsApiController extends Controller
{
    /**
     * Display a listing of the settings.
     */
    public function index(Request $request): JsonResponse
    {
        $group = $request->query('group');
        
        if ($group) {
            $settings = Settings::getByGroup($group);
        } else {
            $settings = Settings::query()
                ->when($request->query('public_only'), function ($query) {
                    return $query->where('is_public', true);
                })
                ->get();
        }

        return response()->json([
            'settings' => SettingsResource::collection($settings),
            'groups' => Settings::select('group')->distinct()->pluck('group'),
        ]);
    }

    /**
     * Get public settings for frontend use
     */
    public function public(): JsonResponse
    {
        return response()->json([
            'settings' => Settings::getPublic(),
        ]);
    }

    /**
     * Store a newly created setting.
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'key' => 'required|string|max:255|unique:settings,key',
            'value' => 'nullable|string',
            'type' => ['required', Rule::in(['text', 'number', 'boolean', 'json'])],
            'description' => 'nullable|string|max:500',
            'group' => 'required|string|max:50',
            'is_public' => 'boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        $setting = Settings::create($validator->validated());

        return response()->json([
            'status' => 'success',
            'message' => 'تم إضافة الإعداد بنجاح',
            'setting' => new SettingsResource($setting),
        ], 201);
    }

    /**
     * Display the specified setting.
     */
    public function show(Settings $setting): JsonResponse
    {
        return response()->json([
            'setting' => new SettingsResource($setting),
        ]);
    }

    /**
     * Update the specified setting.
     */
    public function update(Request $request, Settings $setting): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'value' => 'nullable|string',
            'type' => [Rule::in(['text', 'number', 'boolean', 'json'])],
            'description' => 'nullable|string|max:500',
            'group' => 'string|max:50',
            'is_public' => 'boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        $setting->update($validator->validated());

        return response()->json([
            'status' => 'success',
            'message' => 'تم تحديث الإعداد بنجاح',
            'setting' => new SettingsResource($setting),
        ]);
    }

    /**
     * Update multiple settings at once
     */
    public function updateMultiple(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'settings' => 'required|array',
            'settings.*.key' => 'required|string|exists:settings,key',
            'settings.*.value' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        $updatedSettings = [];
        
        foreach ($request->settings as $settingData) {
            $setting = Settings::where('key', $settingData['key'])->first();
            if ($setting) {
                $setting->update(['value' => $settingData['value']]);
                $updatedSettings[] = new SettingsResource($setting);
            }
        }

        return response()->json([
            'status' => 'success',
            'message' => 'تم تحديث الإعدادات بنجاح',
            'settings' => $updatedSettings,
        ]);
    }

    /**
     * Remove the specified setting.
     */
    public function destroy(Settings $setting): JsonResponse
    {
        $setting->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'تم حذف الإعداد بنجاح',
        ]);
    }

    /**
     * Get settings by group
     */
    public function getByGroup(string $group): JsonResponse
    {
        $settings = Settings::getByGroup($group);

        return response()->json([
            'group' => $group,
            'settings' => SettingsResource::collection($settings),
        ]);
    }

    /**
     * Clear settings cache
     */
    public function clearCache(): JsonResponse
    {
        Settings::clearCache();

        return response()->json([
            'status' => 'success',
            'message' => 'تم مسح ذاكرة التخزين المؤقت للإعدادات بنجاح',
        ]);
    }
}
