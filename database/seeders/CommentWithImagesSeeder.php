<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Comment;
use App\Models\CommentImage;
use Illuminate\Support\Facades\DB;

class CommentWithImagesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Tạo comment có ảnh cho sản phẩm 1
        $commentsWithImages = [
            [
                'user_id' => 13,
                'product_id' => 1,
                'order_id' => 'LL20251220003',
                'rating' => 5,
                'comment' => 'Sản phẩm tuyệt vời! Chất lượng len rất tốt, màu sắc đẹp như hình. Mình đã hoàn thành được chiếc áo len đầu tiên. Cảm ơn shop! Đây là kết quả sau khi đan xong.',
                'images' => ['uudai_sample1.jpg', 'review_sample1.webp']
            ],
            [
                'user_id' => 14,
                'product_id' => 1,
                'order_id' => 'LL20251220004',
                'rating' => 4,
                'comment' => 'Len mềm mại, dễ đan. Màu sắc hơi khác một chút so với hình nhưng vẫn đẹp. Giao hàng nhanh, đóng gói cẩn thận. Mình đã làm được chiếc khăn xinh xắn.',
                'images' => ['review_sample2.webp']
            ],
            [
                'user_id' => 15,
                'product_id' => 2,
                'order_id' => 'LL20251220005',
                'rating' => 5,
                'comment' => 'Combo tự làm rất chi tiết, hướng dẫn dễ hiểu. Mình đã làm được chiếc túi xinh xắn. Chất lượng len A+! Kèm theo ảnh thành phẩm và quá trình làm.',
                'images' => ['review_sample3.jpg', 'uudai_sample1.jpg']
            ],
            [
                'user_id' => 16,
                'product_id' => 3,
                'order_id' => 'LL20251220006',
                'rating' => 4,
                'comment' => 'Sản phẩm đúng như mô tả, chất lượng tốt. Mình rất hài lòng với việc mua hàng lần này. Sẽ tiếp tục ủng hộ shop!',
                'images' => ['review_sample1.webp', 'review_sample2.webp', 'review_sample3.jpg']
            ]
        ];

        // Tạo thêm user nếu chưa có
        $users = [
            ['id' => 13, 'name' => 'Minh Anh', 'email' => 'minhanh@example.com', 'password' => bcrypt('password')],
            ['id' => 14, 'name' => 'Thanh Hoa', 'email' => 'thanhhoa@example.com', 'password' => bcrypt('password')],
            ['id' => 15, 'name' => 'Quỳnh Chi', 'email' => 'quynhchi@example.com', 'password' => bcrypt('password')],
            ['id' => 16, 'name' => 'Bảo Trâm', 'email' => 'baotram@example.com', 'password' => bcrypt('password')]
        ];

        foreach ($users as $user) {
            DB::table('users')->insertOrIgnore($user);
        }

        // Tạo thêm order nếu chưa có
        $orders = [
            [
                'order_id' => 'LL20251220003',
                'user_id' => 13,
                'status' => 'delivered',
                'total_amount' => 150000,
                'created_at' => now()->subDays(10)
            ],
            [
                'order_id' => 'LL20251220004',
                'user_id' => 14,
                'status' => 'delivered',
                'total_amount' => 200000,
                'created_at' => now()->subDays(8)
            ],
            [
                'order_id' => 'LL20251220005',
                'user_id' => 15,
                'status' => 'delivered',
                'total_amount' => 180000,
                'created_at' => now()->subDays(5)
            ],
            [
                'order_id' => 'LL20251220006',
                'user_id' => 16,
                'status' => 'delivered',
                'total_amount' => 250000,
                'created_at' => now()->subDays(3)
            ]
        ];

        foreach ($orders as $order) {
            DB::table('orders')->insertOrIgnore($order);
        }

        // Tạo order items
        $orderItems = [
            ['order_id' => 'LL20251220003', 'product_id' => 1, 'quantity' => 2, 'price' => 75000, 'variant_id' => 1],
            ['order_id' => 'LL20251220004', 'product_id' => 1, 'quantity' => 1, 'price' => 200000, 'variant_id' => 2],
            ['order_id' => 'LL20251220005', 'product_id' => 2, 'quantity' => 1, 'price' => 180000, 'variant_id' => 3],
            ['order_id' => 'LL20251220006', 'product_id' => 3, 'quantity' => 1, 'price' => 250000, 'variant_id' => 4]
        ];

        foreach ($orderItems as $item) {
            DB::table('order_items')->insertOrIgnore($item);
        }

        // Tạo comments với ảnh
        foreach ($commentsWithImages as $commentData) {
            try {
                // Tạo comment
                $comment = Comment::create([
                    'user_id' => $commentData['user_id'],
                    'product_id' => $commentData['product_id'],
                    'order_id' => $commentData['order_id'],
                    'rating' => $commentData['rating'],
                    'comment' => $commentData['comment'],
                    'is_verified' => 1,
                    'is_hidden' => 0
                ]);

                // Thêm ảnh cho comment
                foreach ($commentData['images'] as $imagePath) {
                    CommentImage::create([
                        'comment_id' => $comment->id,
                        'image_path' => $imagePath
                    ]);
                }

                echo "Created comment with images for user {$commentData['user_id']}\n";

            } catch (\Exception $e) {
                echo "Error creating comment: " . $e->getMessage() . "\n";
            }
        }

        echo "Sample comments with images created successfully!\n";
    }
}