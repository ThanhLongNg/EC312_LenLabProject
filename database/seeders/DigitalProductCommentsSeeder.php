<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\DigitalProduct;
use App\Models\DigitalProductPurchase;
use App\Models\Comment;
use App\Models\CommentImage;
use App\Models\CommentReply;

class DigitalProductCommentsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('Đang tạo sample comments cho sản phẩm số...');

        // Tạo test users nếu chưa có
        $users = [];
        $userEmails = [
            'reviewer1@example.com',
            'reviewer2@example.com', 
            'reviewer3@example.com',
            'reviewer4@example.com',
            'reviewer5@example.com'
        ];

        foreach ($userEmails as $email) {
            $user = User::firstOrCreate(
                ['email' => $email],
                [
                    'name' => 'Test User ' . substr($email, 0, strpos($email, '@')),
                    'password' => bcrypt('password123'),
                    'email_verified_at' => now()
                ]
            );
            $users[] = $user;
        }

        // Lấy các sản phẩm số
        $digitalProducts = DigitalProduct::limit(4)->get();

        if ($digitalProducts->isEmpty()) {
            $this->command->error('Không tìm thấy sản phẩm số nào. Vui lòng chạy DigitalProductTestSeeder trước.');
            return;
        }

        // Tạo purchases cho users
        $purchases = [];
        foreach ($digitalProducts as $product) {
            foreach (array_slice($users, 0, 3) as $user) { // 3 users mua mỗi sản phẩm
                $purchase = DigitalProductPurchase::create([
                    'user_id' => $user->id,
                    'digital_product_id' => $product->id,
                    'customer_name' => $user->name,
                    'customer_email' => $user->email,
                    'order_code' => 'DIG-' . strtoupper(substr(md5(uniqid()), 0, 6)),
                    'amount_paid' => $product->price,
                    'purchased_at' => now()->subDays(rand(1, 30)),
                    'expires_at' => now()->addDays($product->access_days),
                    'downloads_count' => rand(0, 2),
                    'email_sent' => true,
                    'download_history' => []
                ]);
                $purchases[] = $purchase;
            }
        }

        // Tạo comments cho sản phẩm số
        $comments = [
            [
                'rating' => 5,
                'comment' => 'Sản phẩm rất tuyệt vời! Hướng dẫn chi tiết, dễ hiểu. Tôi đã hoàn thành được sản phẩm đẹp như mong đợi. Chất lượng file PDF rất tốt, hình ảnh rõ nét.'
            ],
            [
                'rating' => 4,
                'comment' => 'Mẫu móc rất đẹp và độc đáo. Hướng dẫn khá chi tiết nhưng có một vài bước hơi khó hiểu với người mới. Nhìn chung rất hài lòng với sản phẩm này.'
            ],
            [
                'rating' => 5,
                'comment' => 'Tuyệt vời! Đây là lần đầu tôi mua sản phẩm số và rất ấn tượng. File tải về nhanh, chất lượng cao. Sẽ tiếp tục ủng hộ shop.'
            ],
            [
                'rating' => 4,
                'comment' => 'Sản phẩm tốt, giá cả hợp lý. Hướng dẫn step by step rất dễ theo. Chỉ mong shop có thêm nhiều mẫu mới hơn nữa.'
            ],
            [
                'rating' => 5,
                'comment' => 'Chất lượng tuyệt vời! Video hướng dẫn rất rõ ràng, góc quay đẹp. Tôi đã học được nhiều kỹ thuật mới. Rất đáng tiền!'
            ],
            [
                'rating' => 3,
                'comment' => 'Sản phẩm ổn, nhưng mong shop có thể cải thiện thêm phần hướng dẫn cho người mới bắt đầu. Một số thuật ngữ chuyên môn cần giải thích rõ hơn.'
            ],
            [
                'rating' => 5,
                'comment' => 'Xuất sắc! Bộ sưu tập rất đa dạng và phong phú. Mỗi mẫu đều có điểm riêng biệt. File PDF được thiết kế đẹp mắt, dễ đọc.'
            ],
            [
                'rating' => 4,
                'comment' => 'Rất hài lòng với chất lượng sản phẩm. Hướng dẫn chi tiết, hình ảnh minh họa rõ ràng. Sẽ giới thiệu cho bạn bè cùng sở thích.'
            ]
        ];

        $commentIndex = 0;
        foreach ($purchases as $purchase) {
            if ($commentIndex < count($comments)) {
                $commentData = $comments[$commentIndex];
                
                $comment = Comment::create([
                    'user_id' => $purchase->user_id,
                    'digital_product_id' => $purchase->digital_product_id,
                    'digital_purchase_id' => $purchase->id,
                    'rating' => $commentData['rating'],
                    'comment' => $commentData['comment'],
                    'is_verified' => true,
                    'is_hidden' => false,
                    'created_at' => $purchase->purchased_at->addDays(rand(1, 5))
                ]);

                $commentIndex++;
            }
        }

        $this->command->info('✅ Đã tạo thành công:');
        $this->command->info('- ' . count($users) . ' test users');
        $this->command->info('- ' . count($purchases) . ' digital product purchases');
        $this->command->info('- ' . $commentIndex . ' comments cho sản phẩm số');
        $this->command->info('🎯 Bạn có thể đăng nhập với:');
        $this->command->info('   Email: reviewer1@example.com');
        $this->command->info('   Password: password123');
    }
}