<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\DigitalProduct;

class DigitalProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Xóa dữ liệu cũ nếu có
        DigitalProduct::truncate();
        
        $products = [
            [
                'name' => 'Khủng long Dino',
                'description' => 'Mẫu móc khủng long dễ thương với hình ảnh minh họa từng bước chi tiết. Phù hợp cho người mới bắt đầu học móc len.',
                'price' => 50000,
                'type' => 'file',
                'instructions' => 'Tải file PDF và làm theo hướng dẫn từng bước. Cần chuẩn bị len xanh, kim móc số 3.5mm.',
                'auto_send_email' => true,
                'email_template' => 'Cảm ơn bạn đã mua mẫu móc Khủng long Dino! Link tải: {download_link}',
                'thumbnail' => null,
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
                'thumbnail' => null,
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
                'thumbnail' => null,
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
                'name' => 'Móc khóa trái tim len',
                'description' => 'Mẫu móc khóa trái tim dễ thương, phù hợp cho người mới bắt đầu. Có thể làm quà tặng hoặc trang trí.',
                'price' => 0,
                'type' => 'file',
                'instructions' => 'Mẫu miễn phí dành cho thành viên mới. Tải về và thực hành ngay!',
                'auto_send_email' => true,
                'email_template' => 'Chào mừng bạn đến với LENLAB! Đây là mẫu móc miễn phí: {download_link}',
                'thumbnail' => null,
                'is_active' => true,
                'download_limit' => 10,
                'access_days' => 365,
                'files' => [
                    [
                        'name' => 'Móc khóa trái tim.pdf',
                        'path' => 'digital-products/files/heart-keychain.pdf',
                        'size' => 512000,
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
                'thumbnail' => null,
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
                'thumbnail' => null,
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
                'name' => 'Mẫu móc túi xách vintage',
                'description' => 'Hướng dẫn móc túi xách phong cách vintage với họa tiết hoa cổ điển. Kích thước vừa phải, phù hợp đi làm.',
                'price' => 85000,
                'type' => 'file',
                'instructions' => 'File PDF 25 trang với hình ảnh minh họa chi tiết từng bước.',
                'auto_send_email' => true,
                'email_template' => 'Cảm ơn bạn đã mua mẫu túi xách vintage! Link tải: {download_link}',
                'thumbnail' => null,
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
                'name' => 'Khóa học móc amigurumi nâng cao',
                'description' => 'Khóa học chuyên sâu về kỹ thuật móc amigurumi với 15 video bài học và 10 mẫu thực hành.',
                'price' => 450000,
                'type' => 'course',
                'instructions' => 'Truy cập khóa học online với tài khoản cá nhân, học không giới hạn thời gian.',
                'auto_send_email' => true,
                'email_template' => 'Chào mừng bạn đến với khóa học Amigurumi nâng cao! Link: {download_link}',
                'thumbnail' => null,
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
                'name' => 'Bộ mẫu trang trí Noel',
                'description' => '20 mẫu trang trí Noel bằng len: cây thông, ông già Noel, tuần lộc, tất Noel và nhiều hơn nữa.',
                'price' => 120000,
                'type' => 'file',
                'instructions' => 'Bộ sưu tập hoàn chỉnh cho mùa Giáng sinh, phù hợp trang trí nhà cửa.',
                'auto_send_email' => true,
                'email_template' => 'Chúc mừng Giáng sinh! Bộ mẫu trang trí Noel: {download_link}',
                'thumbnail' => null,
                'is_active' => true,
                'download_limit' => 5,
                'access_days' => 120,
                'files' => [
                    [
                        'name' => 'Bộ mẫu trang trí Noel.zip',
                        'path' => 'digital-products/files/christmas-decorations.zip',
                        'size' => 18432000,
                        'uploaded_at' => now()->toISOString()
                    ]
                ]
            ],
            [
                'name' => 'Mẫu móc hoa tulip miễn phí',
                'description' => 'Mẫu móc hoa tulip đơn giản, dễ thương. Phù hợp làm quà tặng 8/3 hoặc trang trí bàn làm việc.',
                'price' => 0,
                'type' => 'file',
                'instructions' => 'Mẫu miễn phí nhân dịp 8/3. Chỉ cần 2 giờ để hoàn thành!',
                'auto_send_email' => true,
                'email_template' => 'Chúc mừng ngày 8/3! Mẫu hoa tulip miễn phí: {download_link}',
                'thumbnail' => null,
                'is_active' => true,
                'download_limit' => 15,
                'access_days' => 365,
                'files' => [
                    [
                        'name' => 'Mẫu hoa tulip.pdf',
                        'path' => 'digital-products/files/tulip-flower.pdf',
                        'size' => 768000,
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
                'thumbnail' => null,
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
            ],
            [
                'name' => 'Bộ mẫu móc đồ chơi cho trẻ em',
                'description' => '25 mẫu đồ chơi an toàn cho trẻ em: xe hơi, máy bay, búp bê, bóng mềm và nhiều hơn nữa.',
                'price' => 180000,
                'type' => 'file',
                'instructions' => 'Tất cả mẫu đều được thiết kế an toàn cho trẻ em, không có chi tiết nhỏ dễ nuốt.',
                'auto_send_email' => true,
                'email_template' => 'Bộ mẫu đồ chơi an toàn cho bé: {download_link}',
                'thumbnail' => null,
                'is_active' => true,
                'download_limit' => 5,
                'access_days' => 180,
                'files' => [
                    [
                        'name' => 'Bộ mẫu đồ chơi trẻ em.zip',
                        'path' => 'digital-products/files/kids-toys-collection.zip',
                        'size' => 22528000,
                        'uploaded_at' => now()->toISOString()
                    ]
                ]
            ]
        ];

        foreach ($products as $product) {
            DigitalProduct::create($product);
        }
        
        $this->command->info('Đã tạo ' . count($products) . ' sản phẩm số mẫu!');
    }
}