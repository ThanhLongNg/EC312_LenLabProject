<?php

namespace App\Http\Controllers;

use App\Models\Province;
use App\Models\Ward;
use Illuminate\Http\Request;

class LocationController extends Controller
{
    // Get all provinces
    public function getProvinces()
    {
        try {
            $provinces = Province::select('id', 'name', 'slug')
                ->orderBy('name')
                ->get();

            return response()->json([
                'success' => true,
                'provinces' => $provinces
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Không thể tải danh sách tỉnh/thành phố'
            ], 500);
        }
    }

    // Get wards by province
    public function getWardsByProvince($provinceId)
    {
        try {
            $wards = Ward::where('province_id', $provinceId)
                ->select('id', 'name')
                ->orderBy('name')
                ->get();

            return response()->json([
                'success' => true,
                'wards' => $wards
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Không thể tải danh sách xã/phường'
            ], 500);
        }
    }

    // Get ward by slug (for backward compatibility)
    public function getWardsByProvinceSlug($provinceSlug)
    {
        try {
            $province = Province::where('slug', $provinceSlug)->first();
            
            if (!$province) {
                return response()->json([
                    'success' => false,
                    'message' => 'Không tìm thấy tỉnh/thành phố'
                ], 404);
            }

            $wards = Ward::where('province_id', $province->id)
                ->select('id', 'name')
                ->orderBy('name')
                ->get();

            return response()->json([
                'success' => true,
                'wards' => $wards
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Không thể tải danh sách xã/phường'
            ], 500);
        }
    }
}