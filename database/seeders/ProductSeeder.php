<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;

class ProductSeeder extends Seeder
{
    public function run()
    {
        $products = [
            [
                'name' => 'Áo Len Cổ Lọ Handknit',
                'price' => 850000,
                'quantity' => 10,
                'image' => 'ao-len-co-lo.jpg',
                'images' => json_encode(['ao-len-co-lo-2.jpg', 'ao-len-co-lo-3.jpg', 'ao-len-co-lo-4.jpg']),
                'description' => 'Áo len cổ lọ được đan thủ công từ len cao cấp, mềm mại và ấm áp.',
                'status' => 1,
                'category_id' => 3,
                'color' => 'Beige',
                'size' => 'M',
                'new' => 1
            ],
            [
                'name' => 'Mũ Len Beanie Basic',
                'price' => 320000,
                'quantity' => 25,
                'image' => 'mu-len-beanie.jpg',
                'images' => json_encode(['mu-len-beanie-2.jpg', 'mu-len-beanie-3.jpg']),
                'description' => 'Mũ len beanie cơ bản, phù hợp cho mọi lứa tuổi.',
                'status' => 1,
                'category_id' => 3,
                'color' => 'White',
                'size' => 'Free Size',
                'new' => 0
            ],
            [
                'name' => 'Tất Len Cổ Cao',
                'price' => 150000,
                'quantity' => 30,
                'image' => 'tat-len-co-cao.jpg',
                'images' => json_encode(['tat-len-co-cao-2.jpg', 'tat-len-co-cao-3.jpg', 'tat-len-co-cao-4.jpg']),
                'description' => 'Tất len cổ cao ấm áp, chất liệu len tự nhiên.',
                'status' => 1,
                'category_id' => 3,
                'color' => 'Gray',
                'size' => 'Free Size',
                'new' => 0
            ],
            [
                'name' => 'Túi Tote Crochet Hoa',
                'price' => 600000,
                'quantity' => 8,
                'image' => 'tui-tote-crochet.jpg',
                'images' => json_encode(['tui-tote-crochet-2.jpg', 'tui-tote-crochet-3.jpg', 'tui-tote-crochet-4.jpg', 'tui-tote-crochet-5.jpg']),
                'description' => 'Túi tote được móc thủ công với họa tiết hoa xinh xắn.',
                'status' => 1,
                'category_id' => 2,
                'color' => 'Beige',
                'size' => 'Medium',
                'new' => 1
            ],
            [
                'name' => 'Len Cotton Cao Cấp',
                'price' => 50000,
                'quantity' => 100,
                'image' => 'len-cotton-cao-cap.jpg',
                'images' => json_encode(['len-cotton-cao-cap-2.jpg', 'len-cotton-cao-cap-3.jpg', 'len-cotton-cao-cap-4.jpg', 'len-cotton-cao-cap-5.jpg']),
                'description' => 'Len cotton cao cấp, mềm mại và dễ đan móc.',
                'status' => 1,
                'category_id' => 1,
                'color' => 'Multi',
                'size' => '100g',
                'new' => 0
            ],
            [
                'name' => 'Thú Bông Gấu Handmade',
                'price' => 280000,
                'quantity' => 15,
                'image' => 'thu-bong-gau.jpg',
                'images' => json_encode(['thu-bong-gau-2.jpg', 'thu-bong-gau-3.jpg']),
                'description' => 'Thú bông gấu được đan thủ công, an toàn cho trẻ em.',
                'status' => 1,
                'category_id' => 6,
                'color' => 'Brown',
                'size' => '25cm',
                'new' => 1
            ],
            [
                'name' => 'Kim Đan Inox Cao Cấp',
                'price' => 25000,
                'quantity' => 50,
                'image' => 'kim-dan-inox.jpg',
                'images' => json_encode(['kim-dan-inox-2.jpg', 'kim-dan-inox-3.jpg']),
                'description' => 'Bộ kim đan inox cao cấp, bền và mịn.',
                'status' => 1,
                'category_id' => 1,
                'color' => 'Silver',
                'size' => 'Set 5 đôi',
                'new' => 0
            ],
            [
                'name' => 'Sách Hướng Dẫn Đan Cơ Bản',
                'price' => 85000,
                'quantity' => 20,
                'image' => 'sach-huong-dan.jpg',
                'images' => json_encode(['sach-huong-dan-2.jpg', 'sach-huong-dan-3.jpg', 'sach-huong-dan-4.jpg']),
                'description' => 'Sách hướng dẫn đan móc từ cơ bản đến nâng cao.',
                'status' => 1,
                'category_id' => 5,
                'color' => 'Multi',
                'size' => 'A4',
                'new' => 0
            ],
            [
                'name' => 'Khăn Choàng Len Dệt Tay',
                'price' => 450000,
                'quantity' => 12,
                'image' => 'khan-choang-len.jpg',
                'images' => json_encode(['khan-choang-len-2.jpg', 'khan-choang-len-3.jpg', 'khan-choang-len-4.jpg', 'khan-choang-len-5.jpg', 'khan-choang-len-6.jpg']),
                'description' => 'Khăn choàng len dệt tay với họa tiết truyền thống, ấm áp và thời trang.',
                'status' => 1,
                'category_id' => 3,
                'color' => 'Multi',
                'size' => '180x60cm',
                'new' => 1
            ],
            [
                'name' => 'Combo Đan Móc Cho Người Mới',
                'price' => 180000,
                'quantity' => 25,
                'image' => 'combo-dan-moc.jpg',
                'images' => json_encode(['combo-dan-moc-2.jpg', 'combo-dan-moc-3.jpg', 'combo-dan-moc-4.jpg']),
                'description' => 'Combo đầy đủ dụng cụ đan móc dành cho người mới bắt đầu.',
                'status' => 1,
                'category_id' => 4,
                'color' => 'Multi',
                'size' => 'Set đầy đủ',
                'new' => 1
            ],
            [
                'name' => 'Găng Tay Len Cảm Ứng',
                'price' => 120000,
                'quantity' => 40,
                'image' => 'gang-tay-cam-ung.jpg',
                'images' => json_encode(['gang-tay-cam-ung-2.jpg', 'gang-tay-cam-ung-3.jpg']),
                'description' => 'Găng tay len có thể sử dụng màn hình cảm ứng, tiện lợi trong mùa đông.',
                'status' => 1,
                'category_id' => 3,
                'color' => 'Black',
                'size' => 'Free Size',
                'new' => 0
            ],
            [
                'name' => 'Đệm Ghế Len Móc Hoa',
                'price' => 220000,
                'quantity' => 18,
                'image' => 'dem-ghe-len.jpg',
                'images' => json_encode(['dem-ghe-len-2.jpg', 'dem-ghe-len-3.jpg', 'dem-ghe-len-4.jpg', 'dem-ghe-len-5.jpg']),
                'description' => 'Đệm ghế len móc với họa tiết hoa, trang trí nhà cửa xinh xắn.',
                'status' => 1,
                'category_id' => 2,
                'color' => 'Cream',
                'size' => '40x40cm',
                'new' => 1
            ],
            [
                'name' => 'Len Alpaca Cao Cấp',
                'price' => 95000,
                'quantity' => 60,
                'image' => 'len-alpaca.jpg',
                'description' => 'Len alpaca cao cấp nhập khẩu, mềm mại và ấm áp tuyệt vời.',
                'status' => 1,
                'category_id' => 1,
                'color' => 'Natural',
                'size' => '50g',
                'new' => 0
            ]
        ];

        foreach ($products as $product) {
            Product::create($product);
        }
    }
}