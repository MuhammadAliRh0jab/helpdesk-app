<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    public function index()
    {
        $messageLimit = Setting::where('key', 'pengadu_message_limit')->first() ?? new Setting(['key' => 'pengadu_message_limit', 'value' => '10']);
        return view('settings.index', compact('messageLimit'));
    }

    public function updateMessageLimit(Request $request)
    {
        $request->validate([
            'pengadu_message_limit' => 'required|integer|min:1',
        ]);

        Setting::updateOrCreate(
            ['key' => 'pengadu_message_limit'],
            [
                'value' => $request->pengadu_message_limit,
                'description' => 'Batas maksimum pesan yang dapat dikirim oleh pengadu sebelum balasan pegawai',
                'updated_at' => now(),
            ]
        );

        return redirect()->route('settings.index')->with('success', 'Batas pesan berhasil diperbarui.');
    }
}