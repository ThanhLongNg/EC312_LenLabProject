<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\Order;
use App\Models\Cart;
use Carbon\Carbon;

class CustomerController extends Controller
{
    // Danh sách khách hàng với thống kê nâng cao
    public function index(Request $request)
    {
        $query = User::query();
        
        // Search functionality
        if ($request->filled('q')) {
            $searchTerm = $request->q;
            $query->where(function ($q) use ($searchTerm) {
                $q->where('name', 'like', "%{$searchTerm}%")
                  ->orWhere('email', 'like', "%{$searchTerm}%")
                  ->orWhere('phone', 'like', "%{$searchTerm}%");
            });
        }
        
        // Filter by registration date
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }
        
        // Filter by gender
        if ($request->filled('gender')) {
            $query->where('gender', $request->gender);
        }
        
        // Filter by order status
        if ($request->filled('order_status')) {
            switch ($request->order_status) {
                case 'has_orders':
                    $query->whereHas('orders');
                    break;
                case 'no_orders':
                    $query->whereDoesntHave('orders');
                    break;
                case 'recent_orders':
                    $query->whereHas('orders', function ($q) {
                        $q->where('created_at', '>=', now()->subDays(30));
                    });
                    break;
            }
        }
        
        // Sorting
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');
        
        switch ($sortBy) {
            case 'name':
                $query->orderBy('name', $sortOrder);
                break;
            case 'email':
                $query->orderBy('email', $sortOrder);
                break;
            case 'orders_count':
                $query->withCount('orders')->orderBy('orders_count', $sortOrder);
                break;
            case 'total_spent':
                $query->withSum('orders', 'total_amount')->orderBy('orders_sum_total_amount', $sortOrder);
                break;
            default:
                $query->orderBy('created_at', $sortOrder);
        }
        
        // Get customers with additional data
        $customers = $query->with(['orders' => function ($q) {
            $q->latest()->limit(3);
        }])
        ->withCount('orders')
        ->withSum('orders', 'total_amount')
        ->paginate(20)
        ->appends($request->query());
        
        // Get statistics
        $stats = $this->getCustomerStats();
        
        return view('admin.customers.index_advanced', compact('customers', 'stats'));
    }
    
    // Get customer statistics
    private function getCustomerStats()
    {
        $totalCustomers = User::count();
        $newThisMonth = User::whereMonth('created_at', now()->month)
                           ->whereYear('created_at', now()->year)
                           ->count();
        
        $activeCustomers = User::whereHas('orders', function ($q) {
            $q->where('created_at', '>=', now()->subDays(90));
        })->count();
        
        $customersWithOrders = User::has('orders')->count();
        
        $avgOrderValue = Order::avg('total_amount') ?? 0;
        
        $topSpenders = User::withSum('orders', 'total_amount')
                          ->orderBy('orders_sum_total_amount', 'desc')
                          ->limit(5)
                          ->get();
        
        return [
            'total_customers' => $totalCustomers,
            'new_this_month' => $newThisMonth,
            'active_customers' => $activeCustomers,
            'customers_with_orders' => $customersWithOrders,
            'avg_order_value' => $avgOrderValue,
            'top_spenders' => $topSpenders,
        ];
    }
    
    // Customer detail view
    public function show($id)
    {
        $customer = User::with(['orders.orderItems.product', 'addresses'])
                       ->withCount('orders')
                       ->withSum('orders', 'total_amount')
                       ->findOrFail($id);
        
        // Get customer's cart items
        $cartItems = Cart::where('user_id', $id)
                        ->with(['product', 'variant'])
                        ->get();
        
        // Get order statistics
        $orderStats = [
            'total_orders' => $customer->orders->count(),
            'total_spent' => $customer->orders->sum('total_amount'),
            'avg_order_value' => $customer->orders->avg('total_amount') ?? 0,
            'last_order_date' => $customer->orders->max('created_at'),
            'favorite_products' => $this->getFavoriteProducts($id),
        ];
        
        return view('admin.customers.show', compact('customer', 'cartItems', 'orderStats'));
    }
    
    // Get customer's favorite products
    private function getFavoriteProducts($userId)
    {
        return DB::table('order_items')
                 ->join('orders', 'order_items.order_id', '=', 'orders.id')
                 ->join('products', 'order_items.product_id', '=', 'products.id')
                 ->where('orders.user_id', $userId)
                 ->select('products.name', 'products.id', DB::raw('SUM(order_items.quantity) as total_quantity'))
                 ->groupBy('products.id', 'products.name')
                 ->orderBy('total_quantity', 'desc')
                 ->limit(5)
                 ->get();
    }

    // Form thêm khách hàng
    public function create()
    {
        return view('admin.customers.create_simple');
    }

    // Lưu khách hàng mới
    public function store(Request $request)
    {
        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email',
            'password' => 'required|min:6',
            'phone'    => 'nullable|string|max:20',
            'gender'   => 'required|in:male,female,other',
            'birth_date' => 'nullable|date'
        ]);

        User::create([
            'name'       => $request->name,
            'email'      => $request->email,
            'password'   => bcrypt($request->password),
            'phone'      => $request->phone,
            'gender'     => $request->gender,
            'birth_date' => $request->birth_date,
        ]);

        return redirect()->route('admin.customers.index')
            ->with('success', 'Thêm khách hàng thành công!');
    }
    
    // Edit customer
    public function edit($id)
    {
        $customer = User::findOrFail($id);
        return view('admin.customers.edit', compact('customer'));
    }
    
    // Update customer
    public function update(Request $request, $id)
    {
        $customer = User::findOrFail($id);
        
        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email,' . $id,
            'phone'    => 'nullable|string|max:20',
            'gender'   => 'required|in:male,female,other',
            'birth_date' => 'nullable|date'
        ]);

        $updateData = [
            'name'       => $request->name,
            'email'      => $request->email,
            'phone'      => $request->phone,
            'gender'     => $request->gender,
            'birth_date' => $request->birth_date,
        ];
        
        // Only update password if provided
        if ($request->filled('password')) {
            $request->validate(['password' => 'min:6']);
            $updateData['password'] = bcrypt($request->password);
        }
        
        $customer->update($updateData);

        return redirect()->route('admin.customers.show', $id)
            ->with('success', 'Cập nhật thông tin khách hàng thành công!');
    }

    // Xóa khách hàng
    public function destroy($id)
    {
        $customer = User::findOrFail($id);
        
        // Check if customer has orders
        if ($customer->orders()->count() > 0) {
            return redirect()->route('admin.customers.index')
                ->with('error', 'Không thể xóa khách hàng đã có đơn hàng!');
        }
        
        // Delete cart items first
        Cart::where('user_id', $id)->delete();
        
        $customer->delete();

        return redirect()->route('admin.customers.index')
            ->with('success', 'Xóa khách hàng thành công!');
    }
    
    // Bulk delete customers
    public function bulkDelete(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:users,id'
        ]);
        
        $customerIds = $request->ids;
        
        // Check if any customers have orders
        $customersWithOrders = User::whereIn('id', $customerIds)
                                  ->has('orders')
                                  ->count();
        
        if ($customersWithOrders > 0) {
            return response()->json([
                'success' => false,
                'message' => "Không thể xóa {$customersWithOrders} khách hàng đã có đơn hàng!"
            ]);
        }
        
        // Delete cart items first
        Cart::whereIn('user_id', $customerIds)->delete();
        
        // Delete customers
        $deletedCount = User::whereIn('id', $customerIds)->delete();
        
        return response()->json([
            'success' => true,
            'message' => "Đã xóa {$deletedCount} khách hàng thành công!"
        ]);
    }
}
