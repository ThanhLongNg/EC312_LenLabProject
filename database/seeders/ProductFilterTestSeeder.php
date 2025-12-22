<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\Comment;
use App\Models\OrderItem;
use App\Models\User;
use App\Models\Order;
use Illuminate\Support\Facades\DB;

class ProductFilterTestSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        echo "Creating sample products for filter testing...\n";

        // Tạo thêm sản phẩm với các category khác nhau
        $sampleProducts = [
            // Category 1: Nguyên phụ liệu - Rating cao, bán chạy
            [
                'id' => 101,
                'name' => 'Len Cotton Premium Màu Xanh',
                'price' => 45000,
                'category_id' => 1,
                'image' => 'product1.1.webp',
                'description' => 'Len cotton cao cấp, mềm mại, phù hợp cho mọi dự án móc len',
                'quantity' => 100,
                'status' => 1,
                'is_active' => 1,
                'new' => 1,
                'target_rating' => 4.8,
                'target_sold' => 150
            ],
            [
                'id' => 102,
                'name' => 'Len Acrylic Đa Màu Set 10 Cuộn',
                'price' => 120000,
                'category_id' => 1,
                'image' => 'product2.1.webp',
                'description' => 'Bộ len acrylic 10 màu cơ bản cho người mới bắt đầu',
                'quantity' => 50,
                'status' => 1,
                'is_active' => 1,
                'new' => 0,
                'target_rating' => 4.2,
                'target_sold' => 80
            ],

            // Category 2: Đồ trang trí - Rating trung bình
            [
                'id' => 103,
                'name' => 'Hoa Len Trang Trí Handmade',
                'price' => 35000,
                'category_id' => 2,
                'image' => 'product3.1.webp',
                'description' => 'Hoa len trang trí đẹp mắt, làm thủ công',
                'quantity' => 30,
                'status' => 1,
                'is_active' => 1,
                'new' => 1,
                'target_rating' => 3.8,
                'target_sold' => 25
            ],
            [
                'id' => 104,
                'name' => 'Móc Khóa Len Hình Thú Cưng',
                'price' => 25000,
                'category_id' => 2,
                'image' => 'product4.1.webp',
                'description' => 'Móc khóa len hình thú cưng dễ thương',
                'quantity' => 40,
                'status' => 1,
                'is_active' => 1,
                'new' => 0,
                'target_rating' => 4.1,
                'target_sold' => 60
            ],

            // Category 3: Thời trang len - Rating cao, giá cao
            [
                'id' => 105,
                'name' => 'Áo Len Cardigan Nữ Cao Cấp',
                'price' => 350000,
                'category_id' => 3,
                'image' => 'product5.1.webp',
                'description' => 'Áo len cardigan nữ thiết kế hiện đại, chất liệu cao cấp',
                'quantity' => 20,
                'status' => 1,
                'is_active' => 1,
                'new' => 1,
                'target_rating' => 4.7,
                'target_sold' => 35
            ],
            [
                'id' => 106,
                'name' => 'Khăn Len Ấm Cổ Unisex',
                'price' => 85000,
                'category_id' => 3,
                'image' => 'product6.1.jpg',
                'description' => 'Khăn len ấm cổ phong cách unisex, phù hợp mọi lứa tuổi',
                'quantity' => 60,
                'status' => 1,
                'is_active' => 1,
                'new' => 0,
                'target_rating' => 4.3,
                'target_sold' => 120
            ],

            // Category 4: Combo tự làm - Bán chạy nhất
            [
                'id' => 107,
                'name' => 'Combo Móc Túi Xách Mini',
                'price' => 180000,
                'category_id' => 4,
                'image' => 'product7.1.jpg',
                'description' => 'Combo đầy đủ nguyên liệu và hướng dẫn móc túi xách mini',
                'quantity' => 25,
                'status' => 1,
                'is_active' => 1,
                'new' => 1,
                'target_rating' => 4.6,
                'target_sold' => 200
            ],
            [
                'id' => 108,
                'name' => 'Combo Làm Thú Bông Gấu Teddy',
                'price' => 220000,
                'category_id' => 4,
                'image' => 'product8.1.jpg',
                'description' => 'Combo hoàn chỉnh để tự làm gấu teddy dễ thương',
                'quantity' => 15,
                'status' => 1,
                'is_active' => 1,
                'new' => 1,
                'target_rating' => 4.9,
                'target_sold' => 180
            ],

            // Category 5: Sách hướng dẫn móc len - Rating thấp hơn
            [
                'id' => 109,
                'name' => 'Sách Hướng Dẫn Móc Len Cơ Bản',
                'price' => 65000,
                'category_id' => 5,
                'image' => 'product9.1.jpg',
                'description' => 'Sách hướng dẫn móc len từ cơ bản đến nâng cao',
                'quantity' => 80,
                'status' => 1,
                'is_active' => 1,
                'new' => 0,
                'target_rating' => 3.5,
                'target_sold' => 45
            ],
            [
                'id' => 110,
                'name' => 'Video Tutorial Móc Len Nâng Cao',
                'price' => 95000,
                'category_id' => 5,
                'image' => 'product10.1.jpg',
                'description' => 'Khóa học video hướng dẫn móc len nâng cao',
                'quantity' => 100,
                'status' => 1,
                'is_active' => 1,
                'new' => 1,
                'target_rating' => 4.0,
                'target_sold' => 30
            ],

            // Category 6: Thú bông len - Rating cao, giá cao
            [
                'id' => 111,
                'name' => 'Thú Bông Len Unicorn Handmade',
                'price' => 280000,
                'category_id' => 6,
                'image' => 'product11.1.jpg',
                'description' => 'Thú bông len unicorn làm thủ công, chi tiết tinh xảo',
                'quantity' => 12,
                'status' => 1,
                'is_active' => 1,
                'new' => 1,
                'target_rating' => 4.8,
                'target_sold' => 25
            ],
            [
                'id' => 112,
                'name' => 'Gấu Bông Len Mini Set 3 Con',
                'price' => 150000,
                'category_id' => 6,
                'image' => 'product12.1.jpg',
                'description' => 'Set 3 gấu bông len mini dễ thương, kích thước 10cm',
                'quantity' => 20,
                'status' => 1,
                'is_active' => 1,
                'new' => 0,
                'target_rating' => 4.4,
                'target_sold' => 70
            ]
        ];

        // Tạo sản phẩm
        foreach ($sampleProducts as $productData) {
            $targetRating = $productData['target_rating'];
            $targetSold = $productData['target_sold'];
            unset($productData['target_rating'], $productData['target_sold']);

            // Kiểm tra xem sản phẩm đã tồn tại chưa
            $existingProduct = Product::find($productData['id']);
            if ($existingProduct) {
                echo "Product {$productData['name']} already exists, skipping...\n";
                continue;
            }

            $product = Product::create($productData);
            echo "Created product: {$product->name}\n";

            // Tạo fake order items để có số lượng bán
            $this->createFakeOrderItems($product->id, $targetSold);

            // Tạo fake comments để có rating
            $this->createFakeComments($product->id, $targetRating);
        }

        echo "Sample products created successfully!\n";
        echo "Products with high rating (4.5+): Len Cotton Premium, Áo Len Cardigan, Combo Làm Thú Bông Gấu, Thú Bông Len Unicorn\n";
        echo "Products with high sales (100+): Len Cotton Premium, Khăn Len Ấm Cổ, Combo Móc Túi Xách, Combo Làm Thú Bông Gấu\n";
    }

    private function createFakeOrderItems($productId, $targetSold)
    {
        // Lấy thông tin sản phẩm
        $product = Product::find($productId);
        if (!$product) return;

        // Tạo nhiều order items với số lượng khác nhau để đạt target
        $remaining = $targetSold;
        $orderCount = 0;

        while ($remaining > 0 && $orderCount < 20) { // Tối đa 20 orders
            $quantity = min($remaining, rand(1, 15));
            $orderId = 'TEST' . date('Ymd') . str_pad($orderCount + 1, 3, '0', STR_PAD_LEFT);

            DB::table('order_items')->insert([
                'order_id' => $orderId,
                'product_id' => $productId,
                'product_name' => $product->name,
                'product_image' => $product->image,
                'quantity' => $quantity,
                'price' => $product->price,
                'total' => $quantity * $product->price
            ]);

            $remaining -= $quantity;
            $orderCount++;
        }
    }

    private function createFakeComments($productId, $targetRating)
    {
        // Tạo 5-15 comments với rating xoay quanh target
        $commentCount = rand(5, 15);
        
        for ($i = 0; $i < $commentCount; $i++) {
            // Tạo rating xoay quanh target (±0.5)
            $rating = max(1, min(5, $targetRating + (rand(-5, 5) / 10)));
            $rating = round($rating);

            $comments = [
                1 => 'Sản phẩm không như mong đợi, chất lượng kém.',
                2 => 'Sản phẩm tạm được, có thể cải thiện thêm.',
                3 => 'Sản phẩm ổn, phù hợp với giá tiền.',
                4 => 'Sản phẩm tốt, chất lượng ổn định.',
                5 => 'Sản phẩm tuyệt vời! Rất hài lòng với chất lượng.'
            ];

            $userId = rand(1, 16);
            $orderId = 'TEST' . date('Ymd') . str_pad(($productId * 100) + $i + 1, 3, '0', STR_PAD_LEFT);

            // Kiểm tra xem comment đã tồn tại chưa
            $existingComment = DB::table('comments')
                ->where('user_id', $userId)
                ->where('product_id', $productId)
                ->where('order_id', $orderId)
                ->first();

            if (!$existingComment) {
                try {
                    DB::table('comments')->insert([
                        'user_id' => $userId,
                        'product_id' => $productId,
                        'order_id' => $orderId,
                        'rating' => $rating,
                        'comment' => $comments[$rating],
                        'is_verified' => 1,
                        'is_hidden' => 0,
                        'created_at' => now()->subDays(rand(1, 30))
                    ]);
                } catch (\Exception $e) {
                    // Skip nếu có lỗi unique constraint
                    continue;
                }
            }
        }
    }
}