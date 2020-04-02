<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Setting;
use Illuminate\Support\Facades\Storage;

class SettingController extends Controller
{
    public function show()
    {
        $setting = Setting::first();

        return response()->json([
            'type' => 'success',
            'data' => $setting
        ], 200);
    }

    public function store(Request $request)
    {

        $request->validate([
            'thousand_separator' => 'different:decimal_separator',
            'decimal_separator' => 'different:thousand_separator',
        ]);

        if (Setting::count() > 0) {
            $setting = Setting::first();
        } else {
            $setting = new Setting;
        }
        $setting->name = $request->name;
        $setting->address = $request->address;

        if (!empty($request->file('logo'))) {

            if (!empty($setting->logo)) {
                if (Storage::disk('images')->exists($setting->logo)) {
                    Storage::disk('images')->delete($setting->logo);
                }
            }

            $file = $request->file('logo');
            $file_extension = $file->getClientOriginalExtension();
            $filename = rand(0, 99).time().'.'.$file_extension;
            $file->storeAs('public/images', $filename);
            $setting->logo = $filename;
            
        }

        $setting->logo_remove = $request->logo_remove;
        $setting->phone_number = $request->phone_number;
        $setting->divider = $request->divider;
        $setting->currency = $request->currency;
        $setting->thousand_separator = $request->thousand_separator;
        $setting->decimal_separator = $request->decimal_separator;
        $setting->tax = $request->tax;
        $setting->printer = $request->printer;
        $setting->save();

        return response()->json([
            'type' => 'success',
            'message' => 'Data berhasil disimpan',
            'data' => $setting
        ], 201);
    }
}
