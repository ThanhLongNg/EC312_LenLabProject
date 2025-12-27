<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Pagination\LengthAwarePaginator;

class UserController extends Controller
{
    // Danh sách người dùng
    public function index(Request $request)
    {
        $q = $request->get('q');
        $status = $request->get('status'); // active | locked | null

        // 1️⃣ Lấy collection đã distinct
        $usersCollection = User::query()
            ->select('users.*')
            ->distinct('users.email') // Use email as unique identifier
            ->when($q, function ($query) use ($q) {
                $query->where(function ($sub) use ($q) {
                    $sub->where('users.name', 'like', "%{$q}%")
                        ->orWhere('users.email', 'like', "%{$q}%")
                        ->orWhere('users.phone', 'like', "%{$q}%")
                        ->orWhereExists(function ($existsQuery) use ($q) {
                            $existsQuery->select(DB::raw(1))
                                ->from('addresses')
                                ->whereColumn('addresses.user_id', 'users.id')
                                ->where('addresses.phone', 'like', "%{$q}%");
                        });
                });
            })
            ->when($status, function ($query) use ($status) {
                if ($status === 'active') {
                    $query->whereNull('users.locked_at');
                }
                if ($status === 'locked') {
                    $query->whereNotNull('users.locked_at');
                }
            })
            ->orderBy('users.id', 'asc')
            ->get();

        // Load relationships for the collection
        $usersCollection->load(['defaultAddress']);
        
        // Add orders count manually
        foreach ($usersCollection as $user) {
            $user->orders_count = $user->orders()->count();
        }

        // 2️⃣ Paginate thủ công
        $page = $request->get('page', 1);
        $perPage = 10;

        $users = new LengthAwarePaginator(
            $usersCollection->forPage($page, $perPage),
            $usersCollection->count(),
            $perPage,
            $page,
            [
                'path' => $request->url(), 
                'query' => $request->query()
            ]
        );

        return view('admin.users.index', [
            'users' => $users,
            'q' => $q,
            'status' => $status,
            'disableCreate' => true, // 👈 CHỈ RIÊNG TRANG NGƯỜI DÙNG
        ]);

    }

    // Chi tiết người dùng + lịch sử mua hàng
    public function show(User $user, Request $request)
    {
        $orderQ = $request->get('order_q');

        $orders = DB::table('orders')
            ->where('user_id', $user->id)
            ->when($orderQ, fn($q) => $q->where('order_id', 'like', "%{$orderQ}%"))
            ->select('order_id','created_at','subtotal','discount_amount','shipping_fee','payment_status')
            ->orderByDesc('created_at')
            ->paginate(8)
            ->withQueryString();

        $totalSpent = DB::table('orders')
            ->where('user_id', $user->id)
            ->selectRaw('COALESCE(SUM(subtotal - discount_amount + shipping_fee),0) as total')
            ->value('total');

        // Lấy địa chỉ mặc định với thông tin province và ward
        $defaultAddress = DB::table('addresses')
            ->leftJoin('provinces', 'addresses.province_id', '=', 'provinces.id')
            ->leftJoin('wards', 'addresses.ward_id', '=', 'wards.id')
            ->where('addresses.user_id', $user->id)
            ->where('addresses.is_default', 1)
            ->select(
                'addresses.*',
                'provinces.name as province_name',
                'wards.name as ward_name'
            )
            ->first();

        // fallback nếu không có is_default
        if (!$defaultAddress) {
            $defaultAddress = DB::table('addresses')
                ->leftJoin('provinces', 'addresses.province_id', '=', 'provinces.id')
                ->leftJoin('wards', 'addresses.ward_id', '=', 'wards.id')
                ->where('addresses.user_id', $user->id)
                ->select(
                    'addresses.*',
                    'provinces.name as province_name',
                    'wards.name as ward_name'
                )
                ->orderByDesc('addresses.id')
                ->first();
        }

        return view('admin.users.show', compact('user', 'orders', 'totalSpent', 'orderQ', 'defaultAddress'));
    }

    // Khóa user
    public function lock(User $user, Request $request)
    {
        $user->locked_at = now();
        $user->lock_reason = $request->lock_reason;
        $user->lock_note = $request->lock_note;
        $user->save();

        return back()->with('success', 'Đã khóa tài khoản');
    }

    // Mở khóa user
    public function unlock(User $user, Request $request)
    {
        $user->locked_at = null;
        $user->lock_reason = $request->lock_reason;
        $user->lock_note = $request->lock_note;
        $user->save();

        return back()->with('success', 'Đã mở khóa tài khoản');
    }
}