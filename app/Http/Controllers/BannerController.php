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
        // 2 vị trí cố định
        $positions = [
            'home' => 'Trang chủ (Homepage)',
            'products' => 'Trang sản phẩm',
        ];

        $banners = Banner::whereIn('position', array_keys($positions))
            ->get()
            ->keyBy('position');

        return view('admin.banners.edit', compact('positions', 'banners'));
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'items' => ['required', 'array'],
            'items.*.position' => ['required', 'in:home,products'],
            'items.*.title' => ['nullable', 'string', 'max:255'],
            'items.*.link' => ['nullable', 'string', 'max:500'],
            'items.*.is_active' => ['nullable', 'boolean'],
            'items.*.image' => ['nullable', 'image', 'max:5120'],
        ]);

        foreach ($data['items'] as $item) {
            $banner = Banner::firstOrCreate(
                ['position' => $item['position']],
                ['is_active' => true]
            );

            $banner->title = $item['title'] ?? $banner->title;
            $banner->link = $item['link'] ?? $banner->link;
            $banner->is_active = (bool)($item['is_active'] ?? false);

            if ($request->hasFile("items.{$item['position']}.image")) {
                // cách này chỉ đúng nếu name input theo key position (mình setup view bên dưới đúng kiểu này)
            }

            $banner->save();
        }

        // xử lý upload theo key position (đúng với view mình đưa)
        foreach (['home', 'products'] as $pos) {
            if ($request->hasFile("items.$pos.image")) {
                $banner = Banner::firstOrCreate(['position' => $pos], ['is_active' => true]);

                if ($banner->image) {
                    Storage::disk('public')->delete($banner->image);
                }

                $path = $request->file("items.$pos.image")->store('uploads/banners', 'public');
                $banner->image = $path;

                $banner->title = $request->input("items.$pos.title");
                $banner->link = $request->input("items.$pos.link");
                $banner->is_active = (bool)$request->input("items.$pos.is_active", 0);

                $banner->save();
            } else {
                // không upload ảnh vẫn update text + active
                $banner = Banner::firstOrCreate(['position' => $pos], ['is_active' => true]);
                $banner->title = $request->input("items.$pos.title");
                $banner->link = $request->input("items.$pos.link");
                $banner->is_active = (bool)$request->input("items.$pos.is_active", 0);
                $banner->save();
            }
        }

        return back()->with('success', 'Đã lưu cấu hình banner');
    }
}
