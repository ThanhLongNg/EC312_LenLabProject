<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\DigitalProduct;

class DigitalProductTestSeeder extends Seeder
{
    /**
     * Run the database seeds for testing digital products page.
     */
    public function run(): void
    {
        // Xóa dữ liệu cũ an toàn (không dùng truncate vì có foreign key)
        \DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DigitalProduct::truncate();
        \DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        
        $this->command->info('Đang tạo sample data cho sản phẩm số...');
        
        $products = [
            [
                'name' => 'Khủng long Dino',
                'description' => 'Mẫu móc khủng long dễ thương với hình ảnh minh họa từng bước chi tiết. Phù hợp cho người mới bắt đầu học móc len.',
                'price' => 50000,
                'type' => 'file',
                'instructions' => 'Tải file PDF và làm theo hướng dẫn từng bước. Cần chuẩn bị len xanh, kim móc số 3.5mm.',
                'auto_send_email' => true,
                'email_template' => 'Cảm ơn bạn đã mua mẫu móc Khủng long Dino! Link tải: {download_link}',
                'thumbnail' => 'product-img/product1.1.webp',
                'is_active' => true,
                'download_limit' => 3,
                'access_days' => 30,
                'files' => [
                    [
                        'name' => 'Mẫu móc khủng long Dino.pdf',
                        'path' => 'digital-products/files/dino-pattern.pdf',
                        'size' => 2048000,
                        'uploaded_at' => now()->toISOString()
                    ]
                ]
            ],
            [
                'name' => 'Khóa học đan len cơ bản cho người mới',
                'description' => 'Học đan len từ A-Z với 20+ video hướng dẫn chi tiết. Bao gồm các kỹ thuật cơ bản và nâng cao.',
                'price' => 299000,
                'type' => 'course',
                'instructions' => 'Sau khi thanh toán, bạn sẽ nhận được link truy cập khóa học trong 24h.',
                'auto_send_email' => true,
                'email_template' => 'Chào mừng bạn đến với khóa học đan len! Link truy cập: {download_link}',
                'thumbnail' => 'product-img/product2.1.webp',
                'is_active' => true,
                'download_limit' => 1,
                'access_days' => 365,
                'links' => [
                    [
                        'name' => 'Khóa học đan len cơ bản',
                        'url' => 'https://course.lenlab.vn/knitting-basic',
                        'added_at' => now()->toISOString()
                    ]
                ]
            ],
            [
                'name' => 'Tuyển tập 50 mẫu thú bông đan chày nhất 2023',
                'description' => '50 mẫu thú bông dễ thương với hướng dẫn từng bước. Bao gồm gấu, thỏ, mèo, chó và nhiều loài khác.',
                'price' => 150000,
                'type' => 'file',
                'instructions' => 'E-book định dạng PDF với 200+ trang hướng dẫn chi tiết.',
                'auto_send_email' => true,
                'email_template' => 'Cảm ơn bạn đã mua tuyển tập 50 mẫu thú bông! Link tải: {download_link}',
                'thumbnail' => 'product-img/product3.1.webp',
                'is_active' => true,
                'download_limit' => 5,
                'access_days' => 90,
                'files' => [
                    [
                        'name' => '50 mẫu thú bông 2023.pdf',
                        'path' => 'digital-products/files/50-patterns-2023.pdf',
                        'size' => 15360000,
                        'uploaded_at' => now()->toISOString()
                    ]
                ]
            ],
            [
                'name' => 'Video hướng dẫn móc hoa hồng 3D',
                'description' => 'Video chi tiết cách móc hoa hồng 3D đẹp mắt. Thời lượng 45 phút với góc quay rõ nét.',
                'price' => 75000,
                'type' => 'course',
                'instructions' => 'Video HD chất lượng cao, có thể xem offline sau khi tải về.',
                'auto_send_email' => true,
                'email_template' => 'Cảm ơn bạn đã mua video hướng dẫn! Link xem: {download_link}',
                'thumbnail' => 'product-img/product4.1.webp',
                'is_active' => true,
                'download_limit' => 3,
                'access_days' => 60,
                'files' => [
                    [
                        'name' => 'Hướng dẫn móc hoa hồng 3D.mp4',
                        'path' => 'digital-products/files/rose-3d-tutorial.mp4',
                        'size' => 104857600,
                        'uploaded_at' => now()->toISOString()
                    ]
                ]
            ],
            [
                'name' => 'Bộ mẫu móc áo len cho bé',
                'description' => '15 mẫu áo len dễ thương cho bé từ 0-5 tuổi. Bao gồm áo cardigan, áo vest, áo hoodie.',
                'price' => 199000,
                'type' => 'file',
                'instructions' => 'Bộ sưu tập hoàn chỉnh với size chart và hướng dẫn chi tiết.',
                'auto_send_email' => true,
                'email_template' => 'Cảm ơn bạn đã mua bộ mẫu áo len cho bé! Link tải: {download_link}',
                'thumbnail' => 'product-img/product5.1.webp',
                'is_active' => true,
                'download_limit' => 5,
                'access_days' => 180,
                'files' => [
                    [
                        'name' => 'Bộ mẫu áo len cho bé.zip',
                        'path' => 'digital-products/files/baby-sweater-collection.zip',
                        'size' => 25600000,
                        'uploaded_at' => now()->toISOString()
                    ]
                ]
            ],
            [
                'name' => 'Khóa học móc amigurumi nâng cao',
                'description' => 'Khóa học chuyên sâu về kỹ thuật móc amigurumi với 15 video bài học và 10 mẫu thực hành.',
                'price' => 450000,
                'type' => 'course',
                'instructions' => 'Truy cập khóa học online với tài khoản cá nhân, học không giới hạn thời gian.',
                'auto_send_email' => true,
                'email_template' => 'Chào mừng bạn đến với khóa học Amigurumi nâng cao! Link: {download_link}',
                'thumbnail' => 'product-img/product6.1.jpg',
                'is_active' => true,
                'download_limit' => 1,
                'access_days' => 730, // 2 năm
                'links' => [
                    [
                        'name' => 'Khóa học Amigurumi nâng cao',
                        'url' => 'https://course.lenlab.vn/amigurumi-advanced',
                        'added_at' => now()->toISOString()
                    ]
                ]
            ],
            [
                'name' => 'Mẫu móc túi xách vintage',
                'description' => 'Hướng dẫn móc túi xách phong cách vintage với họa tiết hoa cổ điển. Kích thước vừa phải, phù hợp đi làm.',
                'price' => 85000,
                'type' => 'file',
                'instructions' => 'File PDF 25 trang với hình ảnh minh họa chi tiết từng bước.',
                'auto_send_email' => true,
                'email_template' => 'Cảm ơn bạn đã mua mẫu túi xách vintage! Link tải: {download_link}',
                'thumbnail' => 'product-img/product7.1.jpg',
                'is_active' => true,
                'download_limit' => 3,
                'access_days' => 45,
                'files' => [
                    [
                        'name' => 'Mẫu túi xách vintage.pdf',
                        'path' => 'digital-products/files/vintage-bag.pdf',
                        'size' => 3072000,
                        'uploaded_at' => now()->toISOString()
                    ]
                ]
            ],
            [
                'name' => 'Video móc khăn len ombre',
                'description' => 'Hướng dẫn video chi tiết cách móc khăn len với hiệu ứng ombre đẹp mắt. Thời lượng 1 giờ 20 phút.',
                'price' => 95000,
                'type' => 'course',
                'instructions' => 'Video chất lượng 4K, có phụ đề tiếng Việt và bảng màu chi tiết.',
                'auto_send_email' => true,
                'email_template' => 'Cảm ơn bạn đã mua video móc khăn ombre! Link: {download_link}',
                'thumbnail' => 'product-img/product8.1.jpg',
                'is_active' => true,
                'download_limit' => 3,
                'access_days' => 90,
                'files' => [
                    [
                        'name' => 'Video móc khăn ombre.mp4',
                        'path' => 'digital-products/files/ombre-scarf-tutorial.mp4',
                        'size' => 157286400,
                        'uploaded_at' => now()->toISOString()
                    ]
                ]
            ]
        ];

        foreach ($products as $index => $product) {
            DigitalProduct::create($product);
            $this->command->info('✓ Đã tạo: ' . $product['name']);
        }
        
        $this->command->info('🎉 Hoàn thành! Đã tạo ' . count($products) . ' sản phẩm số trả phí.');
        $this->command->info('📱 Truy cập: http://localhost:8000/san-pham-so để xem kết quả');
    }
}