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
                'description' => 'Sách hướng dẫn đan móc từ cơ bản đến nâng cao.',
                'status' => 1,
                'category_id' => 5,
                'color' => 'Multi',
                'size' => 'A4',
                'new' => 0
            ]
        ];

        foreach ($products as $product) {
            Product::create($product);
        }
    }
}