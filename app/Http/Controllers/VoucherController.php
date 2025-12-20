<?php

namespace App\Http\Controllers;

use App\Models\Voucher;
use Illuminate\Http\Request;
use Carbon\Carbon;

class VoucherController extends Controller
{
    /**
     * Display vouchers page
     */
    public function index()
    {
        // Get only active vouchers that haven't expired
        $vouchers = Voucher::where('active', true)
                          ->where('end_date', '>', Carbon::now())
                          ->orderBy('start_date', 'desc')
                          ->get();

        return view('vouchers', compact('vouchers'));
    }

    /**
     * Get vouchers via API (for AJAX requests)
     */
    public function getVouchers()
    {
        try {
            $vouchers = Voucher::where('active', true)
                              ->where('end_date', '>', Carbon::now())
                              ->orderBy('start_date', 'desc')
                              ->get();

            return response()->json([
                'success' => true,
                'vouchers' => $vouchers
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Không thể tải danh sách voucher'
            ], 500);
        }
    }

    /**
     * Apply voucher to cart
     */
    public function applyVoucher(Request $request)
    {
        try {
            $validated = $request->validate([
                'voucher_code' => 'required|string',
                'cart_total' => 'nullable|numeric'
            ]);

            \Log::info('Applying voucher', [
                'voucher_code' => $validated['voucher_code'],
                'cart_total' => $validated['cart_total'] ?? 'not provided'
            ]);

            $voucher = Voucher::where('code', $validated['voucher_code'])
                             ->where('active', true)
                             ->where('start_date', '<=', Carbon::now())
                             ->where('end_date', '>', Carbon::now())
                             ->first();

            if (!$voucher) {
                \Log::warning('Voucher not found or expired', [
                    'code' => $validated['voucher_code'],
                    'now' => Carbon::now()->format('Y-m-d H:i:s')
                ]);
                
                return response()->json([
                    'success' => false,
                    'message' => 'Mã voucher không hợp lệ hoặc đã hết hạn'
                ]);
            }

            // Check minimum order value if specified
            if ($voucher->min_order_value && isset($validated['cart_total'])) {
                $cartTotal = floatval($validated['cart_total']);
                
                \Log::info('Checking min order value', [
                    'voucher_min' => $voucher->min_order_value,
                    'cart_total' => $cartTotal,
                    'can_apply' => $cartTotal >= $voucher->min_order_value
                ]);
                
                if ($cartTotal < $voucher->min_order_value) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Đơn hàng tối thiểu ' . number_format($voucher->min_order_value) . 'đ để sử dụng voucher này'
                    ]);
                }
            }

            // Store voucher in session
            session(['applied_voucher' => $voucher->toArray()]);
            
            // Calculate and store discount amount in session
            $discountAmount = 0;
            if ($voucher->type === 'percent' || $voucher->type === 'percentage') {
                session(['voucher_discount_type' => 'percent']);
                session(['voucher_discount_value' => $voucher->discount_value]);
            } else {
                $discountAmount = $voucher->discount_value;
                session(['voucher_discount' => $discountAmount]);
                session(['voucher_discount_type' => 'fixed']);
                session(['voucher_discount_value' => $voucher->discount_value]);
            }

            \Log::info('Voucher applied successfully', [
                'voucher_code' => $voucher->code,
                'type' => $voucher->type,
                'discount_value' => $voucher->discount_value
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Áp dụng voucher thành công',
                'voucher' => $voucher
            ]);

        } catch (\Exception $e) {
            \Log::error('Error applying voucher', [
                'error' => $e->getMessage(),
                'request' => $request->all()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi áp dụng voucher: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove applied voucher
     */
    public function removeVoucher()
    {
        session()->forget(['applied_voucher', 'voucher_discount', 'voucher_discount_type', 'voucher_discount_value']);
        
        return response()->json([
            'success' => true,
            'message' => 'Đã hủy voucher'
        ]);
    }

    /**
     * Get icon type based on voucher type
     */
    public static function getIconType($type)
    {
        switch ($type) {
            case 'free_shipping':
                return 'local_shipping';
            case 'percentage':
                return 'percent';
            case 'fixed_amount':
                return 'payments';
            case 'gift':
                return 'card_giftcard';
            default:
                return 'local_offer';
        }
    }

    /**
     * Get icon color based on voucher type
     */
    public static function getIconColor($type)
    {
        switch ($type) {
            case 'free_shipping':
                return 'shipping-icon'; // orange gradient
            case 'percentage':
                return 'discount-icon'; // yellow gradient
            case 'fixed_amount':
                return 'money-icon'; // green gradient
            case 'gift':
                return 'gift-icon'; // blue gradient
            default:
                return 'discount-icon';
        }
    }
}