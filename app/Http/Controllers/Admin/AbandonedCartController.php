<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\AbandonedCartReminderMail;
use App\Models\Cart;
use App\Models\User;
use App\Models\Voucher;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class AbandonedCartController extends Controller
{
    public function index(Request $request)
    {
        $threshold = now()->subHours(24);

        $q = trim((string) $request->get('q', ''));
        $sortValue = $request->get('sort_value', 'desc'); // desc|asc
        $sortTime  = $request->get('sort_time', 'desc');  // desc newest, asc oldest

        $base = Cart::query()
            ->whereNotNull('user_id')
            ->where('updated_at', '<=', $threshold);

        if ($q !== '') {
            $base->whereHas('user', function ($u) use ($q) {
                $u->where('name', 'like', "%{$q}%")
                  ->orWhere('email', 'like', "%{$q}%")
                  ->orWhere('phone', 'like', "%{$q}%");
            });
        }

        $groupsQuery = $base
            ->select([
                'user_id',
                DB::raw('SUM(quantity * price_at_time) as cart_total'),
                DB::raw('MAX(updated_at) as last_activity'),
                DB::raw('COUNT(*) as lines_count'),
            ])
            ->groupBy('user_id');

        // Order
        $groupsQuery->orderBy('cart_total', $sortValue === 'asc' ? 'asc' : 'desc')
                    ->orderBy('last_activity', $sortTime === 'asc' ? 'asc' : 'desc');

        $rows = $groupsQuery->paginate(10)->withQueryString();

        $userIds = collect($rows->items())->pluck('user_id')->filter()->values();
        $users = User::query()->whereIn('id', $userIds)->get()->keyBy('id');

        // Lấy items theo user (để hiển thị “Sản phẩm trong giỏ”)
        $cartItems = Cart::query()
            ->with(['product', 'variant'])
            ->whereIn('user_id', $userIds)
            ->where('updated_at', '<=', $threshold)
            ->orderByDesc('updated_at')
            ->get()
            ->groupBy('user_id');

        return view('admin.abandoned_carts.index', [
            'rows' => $rows,
            'users' => $users,
            'cartItems' => $cartItems,
            'threshold' => $threshold,
            'q' => $q,
            'sortValue' => $sortValue,
            'sortTime' => $sortTime,
        ]);
    }

    public function sendReminder(User $user)
    {
        $threshold = now()->subHours(24);

        $items = Cart::query()
            ->with(['product', 'variant'])
            ->where('user_id', $user->id)
            ->where('updated_at', '<=', $threshold)
            ->orderByDesc('updated_at')
            ->get();

        if ($items->isEmpty()) {
            return back()->with('error', 'Không có giỏ hàng bỏ quên (>= 24h) cho khách này.');
        }

        $total = $items->sum(fn ($i) => (float)$i->price_at_time * (int)$i->quantity);

        // Direct send (dễ nhất)
        Mail::to($user->email)->send(new AbandonedCartReminderMail($user, $items, $total, null));

        // Nếu sau này muốn Queue: đổi dòng trên thành:
        // Mail::to($user->email)->queue(new AbandonedCartReminderMail($user, $items, $total, null));

        return back()->with('success', 'Đã gửi email nhắc giỏ hàng.');
    }

}
