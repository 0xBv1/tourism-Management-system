<?php

namespace App\Http\Controllers\Dashboard;

use App\Enums\SettingKey;
use App\Http\Controllers\Controller;
use App\Http\Requests\Dashboard\SettingsRequest;
use App\Models\Setting;
use App\Services\Cache\AppCache;

class SettingController extends Controller
{
    public function show()
    {
        $settings = Setting::all();
        return view('dashboard.settings.show', compact('settings'));
    }

    public function update(SettingsRequest $request)
    {
        foreach (SettingKey::all() as $key) {
            Setting::where('option_key', $key)->updateOrCreate([
                'option_key' => $key,
            ], [
                'option_key' => $key,
                'option_value' => $request->get($key)
            ]);
        }
        session()->flash('message', 'Settings Updated Successfully!');
        session()->flash('type', 'success');
        AppCache::forgetBulk('settings_');
        return back();
    }
}
