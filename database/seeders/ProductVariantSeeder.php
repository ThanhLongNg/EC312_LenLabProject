<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProductVariantSeeder extends Seeder
{
    public function run()
    {
        // Kiểm tra xem bảng product_variants có tồn tại không
        if (!DB::getSchemaBuilder()->hasTable('product_variants')) {
            $this->command->info('Bảng product_variants không tồn tại. Bỏ qua seeder này.');
            return;
        }

        // Xóa dữ liệu cũ
        DB::table('product_variants')->truncate();

        $variants = [
            // Sản phẩm 1: Áo len nữ - có nhiều biến thể
            ['product_id' => 1, 'variant_name' => 'Đỏ - Size S', 'price' => 250000, 'image' => 'ao-len-do-s.jpg'],
            ['product_id' => 1, 'variant_name' => 'Đỏ - Size M', 'price' => 250000, 'image' => 'ao-len-do-m.jpg'],
            ['product_id' => 1, 'variant_name' => 'Đỏ - Size L', 'price' => 250000, 'image' => 'ao-len-do-l.jpg'],
            ['product_id' => 1, 'variant_name' => 'Xanh - Size S', 'price' => 250000, 'image' => 'ao-len-xanh-s.jpg'],
            ['product_id' => 1, 'variant_name' => 'Xanh - Size M', 'price' => 250000, 'image' => 'ao-len-xanh-m.jpg'],
            ['product_id' => 1, 'variant_name' => 'Xanh - Size L', 'price' => 250000, 'image' => 'ao-len-xanh-l.jpg'],

            // Sản phẩm 2: Khăn len - có nhiều màu
            ['product_id' => 2, 'variant_name' => 'Màu Hồng', 'price' => 150000, 'image' => 'khan-len-hong.jpg'],
            ['product_id' => 2, 'variant_name' => 'Màu Tím', 'price' => 150000, 'image' => 'khan-len-tim.jpg'],
            ['product_id' => 2, 'variant_name' => 'Màu Cam', 'price' => 150000, 'image' => 'khan-len-cam.jpg'],

            // Sản phẩm 3: Mũ len - có nhiều size
            ['product_id' => 3, 'variant_name' => 'Size S (52-54cm)', 'price' => 120000, 'image' => 'mu-len-s.jpg'],
            ['product_id' => 3, 'variant_name' => 'Size M (54-56cm)', 'price' => 120000, 'image' => 'mu-len-m.jpg'],
            ['product_id' => 3, 'variant_name' => 'Size L (56-58cm)', 'price' => 120000, 'image' => 'mu-len-l.jpg'],

            // Sản phẩm 4: Găng tay len - có nhiều biến thể
            ['product_id' => 4, 'variant_name' => 'Đen - Size S', 'price' => 80000, 'image' => 'gang-tay-den-s.jpg'],
            ['product_id' => 4, 'variant_name' => 'Đen - Size M', 'price' => 80000, 'image' => 'gang-tay-den-m.jpg'],
            ['product_id' => 4, 'variant_name' => 'Trắng - Size S', 'price' => 80000, 'image' => 'gang-tay-trang-s.jpg'],
            ['product_id' => 4, 'variant_name' => 'Trắng - Size M', 'price' => 80000, 'image' => 'gang-tay-trang-m.jpg'],

            // Sản phẩm 5: Chỉ len - có nhiều màu
            ['product_id' => 5, 'variant_name' => 'Chỉ Đỏ', 'price' => 45000, 'image' => 'chi-len-do.jpg'],
            ['product_id' => 5, 'variant_name' => 'Chỉ Xanh Lá', 'price' => 45000, 'image' => 'chi-len-xanh-la.jpg'],
            ['product_id' => 5, 'variant_name' => 'Chỉ Vàng', 'price' => 45000, 'image' => 'chi-len-vang.jpg'],
            ['product_id' => 5, 'variant_name' => 'Chỉ Hồng', 'price' => 45000, 'image' => 'chi-len-hong.jpg'],

            // Sản phẩm 6: Thú bông - có nhiều biến thể
            ['product_id' => 6, 'variant_name' => 'Gấu Nâu - Size S', 'price' => 180000, 'image' => 'gau-nau-s.jpg'],
            ['product_id' => 6, 'variant_name' => 'Gấu Nâu - Size M', 'price' => 200000, 'image' => 'gau-nau-m.jpg'],
            ['product_id' => 6, 'variant_name' => 'Gấu Nâu - Size L', 'price' => 250000, 'image' => 'gau-nau-l.jpg'],
            ['product_id' => 6, 'variant_name' => 'Gấu Kem - Size S', 'price' => 180000, 'image' => 'gau-kem-s.jpg'],
            ['product_id' => 6, 'variant_name' => 'Gấu Kem - Size M', 'price' => 200000, 'image' => 'gau-kem-m.jpg'],

            // Một số sản phẩm không có variants (sản phẩm 7, 8, 9, 10 sẽ không có variants)
        ];

        foreach ($variants as $variant) {
            DB::table('product_variants')->insert($variant);
        }

        $this->command->info('Đã tạo ' . count($variants) . ' product variants.');
    }
}