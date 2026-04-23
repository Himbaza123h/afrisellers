<?php

namespace App\Http\Controllers\Admin\Membership;

use App\Http\Controllers\Controller;
use App\Models\SystemSetting;
use Illuminate\Http\Request;

class MembershipSettingController extends Controller
{
    public function index()
    {
        $settings = SystemSetting::orderBy('key')->paginate(20);

        $groupedSettings = [
            'trial' => SystemSetting::where('key', 'like', 'trial_%')->get(),
            'limits' => SystemSetting::where('key', 'like', '%_limit')->orWhere('key', 'like', 'max_%')->get(),
            'pricing' => SystemSetting::where('key', 'like', '%_price')->get(),
            'features' => SystemSetting::where('key', 'like', 'has_%')->get(),
        ];

        return view('admin.membership.settings.index', compact('settings', 'groupedSettings'));
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'settings' => 'required|array',
            'settings.*.key' => 'required|string',
            'settings.*.value' => 'required',
            'settings.*.type' => 'required|in:integer,decimal,boolean,string',
        ]);

        foreach ($validated['settings'] as $setting) {
            SystemSetting::set(
                $setting['key'],
                $setting['value'],
                $setting['type'],
                $request->input('settings.' . $setting['key'] . '.description')
            );
        }

        return back()->with('success', 'Settings updated successfully!');
    }
}
