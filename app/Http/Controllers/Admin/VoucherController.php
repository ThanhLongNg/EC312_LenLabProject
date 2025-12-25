<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Voucher;
use Illuminate\Http\Request;
use Carbon\Carbon;

class VoucherController extends Controller
{
    public function index(Request $request)
    {
        $query = Voucher::query();

        // Filter by status
        if ($request->has('status') && $request->status !== 'all') {
            switch ($request->status) {
                case 'active':
                    $query->where('active', true);
                    break;
                case 'inactive':
                    $query->where('active', false);
                    break;
                case 'expired':
                    $query->where('end_date', '<', Carbon::now());
                    break;
                case 'upcoming':
                    $query->where('start_date', '>', Carbon::now());
                    break;
            }
        }

        // Filter by type
        if ($request->has('type') && $request->type !== 'all') {
            $query->where('type', $request->type);
        }

        // Search functionality
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('code', 'like', "%{$search}%");
            });
        }

        $vouchers = $query->orderBy('id', 'desc')->paginate(10);

        // Get counts for tabs
        $counts = [
            'all' => Voucher::count(),
            'active' => Voucher::where('active', true)->count(),
            'inactive' => Voucher::where('active', false)->count(),
            'expired' => Voucher::where('end_date', '<', Carbon::now())->count(),
            'upcoming' => Voucher::where('start_date', '>', Carbon::now())->count(),
        ];

        return view('admin.vouchers.index', [
            'vouchers' => $vouchers,
            'counts' => $counts,
            'currentStatus' => $request->get('status', 'all'),
            'currentType' => $request->get('type', 'all'),
            'currentSearch' => $request->get('search'),
            'pageTitle' => 'Quản lý mã giảm giá',
            'custom_header' => true
        ]);
    }

    public function create()
    {
        return view('admin.vouchers.create', [
            'pageTitle' => 'Tạo mã giảm giá mới'
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'code' => 'required|string|max:50|unique:vouchers,code',
            'type' => 'required|in:fixed,percent',
            'discount_value' => 'required|integer|min:1',
            'min_order_value' => 'nullable|integer|min:0',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'active' => 'boolean'
        ]);

        try {
            Voucher::create([
                'code' => strtoupper($request->code),
                'type' => $request->type,
                'discount_value' => $request->discount_value,
                'min_order_value' => $request->min_order_value ?? 0,
                'start_date' => $request->start_date,
                'end_date' => $request->end_date,
                'active' => $request->has('active')
            ]);

            return redirect()->route('admin.vouchers.index')
                ->with('success', 'Mã giảm giá đã được tạo thành công!');

        } catch (\Exception $e) {
            return back()->withInput()
                ->with('error', 'Có lỗi xảy ra: ' . $e->getMessage());
        }
    }

    public function edit(Voucher $voucher)
    {
        return view('admin.vouchers.edit', [
            'voucher' => $voucher,
            'pageTitle' => 'Chỉnh sửa mã giảm giá'
        ]);
    }

    public function update(Request $request, Voucher $voucher)
    {
        $request->validate([
            'code' => 'required|string|max:50|unique:vouchers,code,' . $voucher->id,
            'type' => 'required|in:fixed,percent',
            'discount_value' => 'required|integer|min:1',
            'min_order_value' => 'nullable|integer|min:0',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'active' => 'boolean'
        ]);

        try {
            $voucher->update([
                'code' => strtoupper($request->code),
                'type' => $request->type,
                'discount_value' => $request->discount_value,
                'min_order_value' => $request->min_order_value ?? 0,
                'start_date' => $request->start_date,
                'end_date' => $request->end_date,
                'active' => $request->has('active')
            ]);

            return redirect()->route('admin.vouchers.index')
                ->with('success', 'Mã giảm giá đã được cập nhật thành công!');

        } catch (\Exception $e) {
            return back()->withInput()
                ->with('error', 'Có lỗi xảy ra: ' . $e->getMessage());
        }
    }

    public function destroy(Voucher $voucher)
    {
        try {
            $voucher->delete();

            return response()->json([
                'success' => true,
                'message' => 'Mã giảm giá đã được xóa thành công!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra: ' . $e->getMessage()
            ], 500);
        }
    }

    public function toggleActive(Voucher $voucher)
    {
        try {
            $voucher->update(['active' => !$voucher->active]);

            return response()->json([
                'success' => true,
                'message' => $voucher->active ? 'Mã giảm giá đã được kích hoạt!' : 'Mã giảm giá đã được tắt!',
                'active' => $voucher->active
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra: ' . $e->getMessage()
            ], 500);
        }
    }

    public function bulkAction(Request $request)
    {
        $request->validate([
            'action' => 'required|in:activate,deactivate,delete',
            'voucher_ids' => 'required|array',
            'voucher_ids.*' => 'exists:vouchers,id'
        ]);

        try {
            $vouchers = Voucher::whereIn('id', $request->voucher_ids);
            $count = $vouchers->count();

            switch ($request->action) {
                case 'activate':
                    $vouchers->update(['active' => true]);
                    $message = "Đã kích hoạt {$count} mã giảm giá!";
                    break;

                case 'deactivate':
                    $vouchers->update(['active' => false]);
                    $message = "Đã tắt {$count} mã giảm giá!";
                    break;

                case 'delete':
                    $vouchers->delete();
                    $message = "Đã xóa {$count} mã giảm giá!";
                    break;
            }

            return response()->json([
                'success' => true,
                'message' => $message
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra: ' . $e->getMessage()
            ], 500);
        }
    }

    public function stats()
    {
        $stats = [
            'total_vouchers' => Voucher::count(),
            'active_vouchers' => Voucher::where('active', true)->count(),
            'inactive_vouchers' => Voucher::where('active', false)->count(),
            'expired_vouchers' => Voucher::where('end_date', '<', Carbon::now())->count(),
            'upcoming_vouchers' => Voucher::where('start_date', '>', Carbon::now())->count(),
            'type_distribution' => [
                'fixed' => Voucher::where('type', 'fixed')->count(),
                'percent' => Voucher::where('type', 'percent')->count(),
            ],
            'recent_vouchers' => Voucher::orderBy('id', 'desc')->limit(5)->get()
        ];

        return response()->json([
            'success' => true,
            'stats' => $stats
        ]);
    }
}