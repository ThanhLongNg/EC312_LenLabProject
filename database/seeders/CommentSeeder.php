<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Comment;
use App\Models\CommentImage;
use App\Models\CommentReply;
use App\Models\User;
use App\Models\Product;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Support\Facades\DB;

class CommentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Tạo một số user mẫu nếu chưa có
        $users = [];
        $userNames = [
            'Minh Anh',
            'Hoàng Phúc', 
            'Thế Hà',
            'Tuấn',
            'Mai Linh',
            'Ngọc Hân'
        ];

        foreach ($userNames as $name) {
            $user = User::firstOrCreate([
                'email' => strtolower(str_replace(' ', '', $name)) . '@example.com'
            ], [
                'name' => $name,
                'password' => bcrypt('password123'),
                'email_verified_at' => now()
            ]);
            $users[] = $user;
        }

        // Tạo một số đơn hàng mẫu với status 'delivered'
        $orders = [];
        for ($i = 1; $i <= 6; $i++) {
            // Use consistent format: LL + YYYYMMDD + sequential number
            $orderId = 'LL' . now()->format('Ymd') . str_pad($i, 2, '0', STR_PAD_LEFT);
            
            $order = Order::firstOrCreate([
                'order_id' => $orderId
            ], [
                'user_id' => $users[($i - 1) % count($users)]->id,
                'status' => 'delivered',
                'payment_method' => 'cod',
                'payment_status' => 'completed',
                'subtotal' => rand(100000, 500000),
                'shipping_fee' => 30000,
                'discount_amount' => 0,
                'total_amount' => rand(130000, 530000),
                'email' => $users[($i - 1) % count($users)]->email,
                'full_name' => $users[($i - 1) % count($users)]->name,
                'phone' => '0' . rand(900000000, 999999999),
                'province' => 'TP. Hồ Chí Minh',
                'ward' => 'Phường ' . rand(1, 20),
                'specific_address' => rand(1, 999) . ' Đường ABC'
            ]);
            $orders[] = $order;
        }

        // Lấy một số sản phẩm có sẵn
        $products = Product::take(10)->get();
        
        if ($products->isEmpty()) {
            $this->command->error('Không có sản phẩm nào trong database. Vui lòng tạo sản phẩm trước.');
            return;
        }

        // Tạo order items cho các đơn hàng
        foreach ($orders as $order) {
            $selectedProducts = $products->random(rand(1, 3));
            
            foreach ($selectedProducts as $product) {
                OrderItem::firstOrCreate([
                    'order_id' => $order->order_id,
                    'product_id' => $product->id
                ], [
                    'product_name' => $product->name,
                    'product_image' => $product->image,
                    'variant_id' => null,
                    'variant_info' => null,
                    'quantity' => rand(1, 3),
                    'price' => $product->price,
                    'total' => $product->price * rand(1, 3)
                ]);
            }
        }

        // Tạo comments mẫu
        // Tạo comments mẫu với format order ID nhất quán
        $sampleComments = [
            [
                'rating' => 5,
                'comment' => 'Sản phẩm tuyệt vời! Chất lượng len rất tốt, màu sắc đúng như hình. Mình rất hài lòng với sản phẩm này. Shop gói hàng cẩn thận, giao hàng nhanh. Sẽ ủng hộ shop tiếp!',
                'order_id' => 'LL' . now()->format('Ymd') . '03'
            ],
            [
                'rating' => 4,
                'comment' => 'Len rất đẹp, màu sắc tươi sáng. Chất lượng tốt, không bị xù lông. Tuy nhiên giá hơi cao so với thị trường. Nhưng nhìn chung vẫn hài lòng với sản phẩm.',
                'order_id' => 'LL' . now()->format('Ymd') . '04'
            ],
            [
                'rating' => 5,
                'comment' => 'Tuyệt vời! Mua để làm mũ cho con, len rất mềm và không gây kích ứng da. Con mình rất thích. Màu sắc đẹp, không phai màu sau khi giặt. Recommend!',
                'order_id' => 'LL' . now()->format('Ymd') . '05'
            ],
            [
                'rating' => 3,
                'comment' => 'Sản phẩm ổn, chưa có gì đặc biệt. Chất lượng bình thường, phù hợp với giá tiền. Giao hàng hơi chậm so với dự kiến.',
                'order_id' => 'LL' . now()->format('Ymd') . '06'
            ],
            [
                'rating' => 4,
                'comment' => 'Chất lượng len tốt, đan móc dễ dàng. Màu sắc đẹp mắt. Chỉ có điều hơi ít so với mô tả, may mà vẫn đủ để hoàn thành sản phẩm.',
                'order_id' => 'LL' . now()->format('Ymd') . '01'
            ],
            [
                'rating' => 5,
                'comment' => 'Len chất lượng cao, mềm mịn. Đan lên rất đẹp và bền. Shop tư vấn nhiệt tình, giao hàng nhanh. Sẽ quay lại mua thêm!',
                'order_id' => 'LL' . now()->format('Ymd') . '02'
            ]
        ];

        // Tạo comments cho các sản phẩm
        $commentIndex = 0;
        foreach ($orders as $orderIndex => $order) {
            $orderItems = OrderItem::where('order_id', $order->order_id)->get();
            
            foreach ($orderItems as $item) {
                if ($commentIndex < count($sampleComments) && rand(1, 100) <= 70) { // 70% chance tạo comment
                    $commentData = $sampleComments[$commentIndex % count($sampleComments)];
                    
                    $comment = Comment::create([
                        'user_id' => $order->user_id,
                        'product_id' => $item->product_id,
                        'order_id' => $order->order_id, // Use the actual order ID from the order
                        'rating' => $commentData['rating'],
                        'comment' => $commentData['comment'],
                        'is_verified' => 1,
                        'is_hidden' => 0
                    ]);

                    // Tạo admin reply cho một số comment (30% chance)
                    if (rand(1, 100) <= 30) {
                        $adminReplies = [
                            'Cảm ơn bạn đã đánh giá! Chúng tôi rất vui khi bạn hài lòng với sản phẩm.',
                            'Cảm ơn phản hồi của bạn. Chúng tôi sẽ cải thiện chất lượng dịch vụ tốt hơn.',
                            'Rất cảm ơn bạn đã tin tưởng và ủng hộ shop!',
                            'Cảm ơn bạn đã chia sẻ trải nghiệm. Hy vọng sẽ được phục vụ bạn lần sau!'
                        ];

                        // Tạo admin user nếu chưa có
                        $admin = DB::table('admins')->first();
                        if (!$admin) {
                            DB::table('admins')->insert([
                                'name' => 'Admin LenLab',
                                'email' => 'admin@lenlab.com',
                                'password' => bcrypt('admin123'),
                                'role' => 'admin'
                            ]);
                            $admin = DB::table('admins')->first();
                        }

                        CommentReply::create([
                            'comment_id' => $comment->id,
                            'admin_id' => $admin->id,
                            'reply' => $adminReplies[array_rand($adminReplies)]
                        ]);
                    }

                    $commentIndex++;
                }
            }
        }

        $this->command->info('Đã tạo ' . Comment::count() . ' comments mẫu');
        $this->command->info('Đã tạo ' . CommentReply::count() . ' admin replies mẫu');
        $this->command->info('Đã tạo ' . count($orders) . ' orders với status delivered');
        $this->command->info('Đã tạo ' . count($users) . ' users mẫu');
    }
}