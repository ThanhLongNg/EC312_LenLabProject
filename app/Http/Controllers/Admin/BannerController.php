<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Banner;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class BannerController extends Controller
{
    public function edit()
    {
        // 2 vị trí: home (trang chủ) và campaign (banner ngang ở landing)
        $home = Banner::firstOrCreate(
            ['position' => 'home'],
            ['is_active' => 1]
        );

        $campaign = Banner::firstOrCreate(
            ['position' => 'campaign'],
            ['is_active' => 1]
        );

        return view('admin.banners.edit', compact('home', 'campaign'));
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'position' => ['required', 'in:home,campaign'],
            'link'     => ['nullable', 'string', 'max:500'], // cho phép link rỗng
            'is_active'=> ['nullable', 'boolean'],
            'image'    => ['nullable', 'image', 'max:5120'],
        ]);

        $banner = Banner::where('position', $data['position'])->firstOrFail();

        $banner->link = $data['link'] ?? null;
        $banner->is_active = (bool)($request->input('is_active', false));

        if ($request->hasFile('image')) {
            if ($banner->image) {
                Storage::disk('public')->delete($banner->image);
            }
            $banner->image = $request->file('image')->store('uploads/banners', 'public');
        }

        $banner->save();

        return back()->with('success', 'Đã lưu banner');
    }
}
