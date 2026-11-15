<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    public function show(Request $request): JsonResponse
    {
        $setting = $request->user()->getOrCreateSetting();

        return $this->success($setting->settings ?? $setting::defaults());
    }

    public function update(Request $request): JsonResponse
    {
        $setting = $request->user()->getOrCreateSetting();
        $current = $setting->settings ?? $setting::defaults();

        $setting->update([
            'settings' => array_merge($current, $request->all()),
        ]);

        return $this->success($setting->settings, '设置已更新');
    }
}
