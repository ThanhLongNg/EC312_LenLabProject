<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\FaqItem;

class FaqItemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faqs = [
            // Giao hàng & Vận chuyển
            [
                'category' => 'giao_hang',
                'keywords' => ['giao hàng', 'ship', 'vận chuyển', 'delivery', 'thời gian giao hàng', 'mất bao lâu', 'bao lâu'],
                'question' => 'Thời gian giao hàng mất bao lâu?',
                'answer' => 'Chúng tôi giao hàng toàn quốc trong 2-5 ngày làm việc. Đối với khu vực nội thành TP.HCM và Hà Nội, thời gian giao hàng chỉ 1-2 ngày.',
                'priority' => 90,
                'is_active' => true
            ],
            [
                'category' => 'giao_hang',
                'keywords' => ['phí ship', 'phí giao hàng', 'chi phí vận chuyển', 'shipping fee', 'như thế nào'],
                'question' => 'Phí giao hàng như thế nào?',
                'answer' => 'Phí giao hàng từ 25.000đ tùy theo khu vực. Miễn phí ship cho đơn hàng từ 500.000đ trên toàn quốc.',
                'priority' => 85,
                'is_active' => true
            ],
            [
                'category' => 'giao_hang',
                'keywords' => ['giao hàng nhanh', 'express', 'hỏa tốc'],
                'question' => 'Có dịch vụ giao hàng nhanh không?',
                'answer' => 'Có! Chúng tôi có dịch vụ giao hàng nhanh trong 24h cho khu vực nội thành với phí 50.000đ.',
                'priority' => 70,
                'is_active' => true
            ],

            // Đổi trả & Hoàn tiền
            [
                'category' => 'doi_tra',
                'keywords' => ['đổi trả', 'return', 'exchange', 'hoàn trả'],
                'question' => 'Chính sách đổi trả như thế nào?',
                'answer' => 'Bạn có thể đổi trả sản phẩm trong vòng 7 ngày kể từ khi nhận hàng. Sản phẩm phải còn nguyên tem mác, chưa qua sử dụng và còn đầy đủ bao bì.',
                'priority' => 88,
                'is_active' => true
            ],
            [
                'category' => 'doi_tra',
                'keywords' => ['hoàn tiền', 'refund', 'trả tiền'],
                'question' => 'Khi nào được hoàn tiền?',
                'answer' => 'Chúng tôi hoàn tiền 100% nếu sản phẩm lỗi do nhà sản xuất hoặc giao sai hàng. Thời gian hoàn tiền 3-7 ngày làm việc.',
                'priority' => 82,
                'is_active' => true
            ],
            [
                'category' => 'doi_tra',
                'keywords' => ['bảo hành', 'warranty', 'lỗi sản phẩm'],
                'question' => 'Sản phẩm có được bảo hành không?',
                'answer' => 'Sản phẩm len được bảo hành 30 ngày đối với lỗi kỹ thuật như tuột chỉ, rách không do tác động bên ngoài.',
                'priority' => 75,
                'is_active' => true
            ],

            // Sản phẩm & Chất liệu
            [
                'category' => 'san_pham',
                'keywords' => ['chất liệu', 'material', 'len gì', 'làm từ gì', 'sản phẩm làm'],
                'question' => 'Sản phẩm làm từ chất liệu gì?',
                'answer' => 'Chúng tôi sử dụng len cotton, len wool và len acrylic cao cấp, an toàn cho da, không gây dị ứng. Tất cả đều có chứng nhận chất lượng.',
                'priority' => 80,
                'is_active' => true
            ],
            [
                'category' => 'san_pham',
                'keywords' => ['bảo quản', 'giặt', 'care', 'chăm sóc', 'cách bảo quản', 'sản phẩm len'],
                'question' => 'Cách bảo quản sản phẩm len?',
                'answer' => 'Sản phẩm len nên giặt tay bằng nước lạnh, không vắt mạnh, phơi nơi thoáng mát tránh ánh nắng trực tiếp. Có thể giặt máy ở chế độ nhẹ.',
                'priority' => 78,
                'is_active' => true
            ],
            [
                'category' => 'san_pham',
                'keywords' => ['size', 'kích thước', 'số đo', 'có những size', 'size nào'],
                'question' => 'Có những size nào?',
                'answer' => 'Chúng tôi có đầy đủ size từ S đến XL. Bạn có thể tham khảo bảng size chi tiết trên từng sản phẩm hoặc liên hệ để được tư vấn.',
                'priority' => 76,
                'is_active' => true
            ],
            [
                'category' => 'san_pham',
                'keywords' => ['màu sắc', 'color', 'màu', 'có những màu', 'màu gì'],
                'question' => 'Sản phẩm có những màu gì?',
                'answer' => 'Sản phẩm có nhiều màu sắc đa dạng từ pastel đến tông đậm. Màu sắc thực tế có thể chênh lệch nhẹ so với hình ảnh do ánh sáng chụp.',
                'priority' => 65,
                'is_active' => true
            ],

            // Sản phẩm số
            [
                'category' => 'san_pham',
                'keywords' => ['hướng dẫn', 'tutorial', 'pattern', 'video', 'sản phẩm số', 'có gì', 'digital'],
                'question' => 'Sản phẩm số có gì?',
                'answer' => 'Sản phẩm số bao gồm video hướng dẫn chi tiết và file PDF pattern. Bạn có thể tải về ngay sau khi thanh toán thành công.',
                'priority' => 72,
                'is_active' => true
            ],
            [
                'category' => 'san_pham',
                'keywords' => ['video hướng dẫn', 'video tutorial'],
                'question' => 'Video hướng dẫn như thế nào?',
                'answer' => 'Video hướng dẫn có độ phân giải HD, thời lượng từ 30-90 phút tùy độ phức tạp của sản phẩm. Có phụ đề tiếng Việt.',
                'priority' => 68,
                'is_active' => true
            ],

            // Thanh toán & Bảo mật
            [
                'category' => 'thanh_toan',
                'keywords' => ['thanh toán', 'payment', 'trả tiền', 'phương thức'],
                'question' => 'Có những hình thức thanh toán nào?',
                'answer' => 'Chúng tôi hỗ trợ thanh toán qua thẻ ATM, Visa/Mastercard, ví điện tử MoMo, ZaloPay và COD (thanh toán khi nhận hàng).',
                'priority' => 85,
                'is_active' => true
            ],
            [
                'category' => 'thanh_toan',
                'keywords' => ['bảo mật', 'security', 'an toàn'],
                'question' => 'Thông tin thanh toán có an toàn không?',
                'answer' => 'Thông tin thanh toán được mã hóa SSL 256-bit, đảm bảo an toàn tuyệt đối. Chúng tôi không lưu trữ thông tin thẻ của khách hàng.',
                'priority' => 77,
                'is_active' => true
            ],

            // Hỗ trợ & Liên hệ
            [
                'category' => 'ho_tro',
                'keywords' => ['liên hệ', 'contact', 'hỗ trợ', 'support', 'hotline'],
                'question' => 'Làm sao để liên hệ hỗ trợ?',
                'answer' => 'Bạn có thể liên hệ qua hotline: 1900-xxxx hoặc email: support@lenlab.vn. Chúng tôi hỗ trợ từ 8h-22h hàng ngày.',
                'priority' => 90,
                'is_active' => true
            ],
            [
                'category' => 'ho_tro',
                'keywords' => ['giờ làm việc', 'working hours', 'thời gian'],
                'question' => 'Giờ làm việc của shop?',
                'answer' => 'Chúng tôi làm việc từ 8h-22h từ thứ 2 đến chủ nhật. Đơn hàng đặt sau 22h sẽ được xử lý vào sáng hôm sau.',
                'priority' => 60,
                'is_active' => true
            ],

            // Tổng quát
            [
                'category' => 'general',
                'keywords' => ['giới thiệu', 'about', 'lenlab', 'shop'],
                'question' => 'LENLAB là gì?',
                'answer' => 'LENLAB là thương hiệu chuyên về sản phẩm len handmade cao cấp, từ thú bông, túi xách đến áo len. Chúng tôi cam kết chất lượng và sự hài lòng của khách hàng.',
                'priority' => 50,
                'is_active' => true
            ],
            [
                'category' => 'general',
                'keywords' => ['khuyến mãi', 'promotion', 'discount', 'sale'],
                'question' => 'Có chương trình khuyến mãi nào không?',
                'answer' => 'Chúng tôi thường xuyên có các chương trình khuyến mãi vào dịp lễ tết. Theo dõi fanpage để cập nhật thông tin mới nhất!',
                'priority' => 65,
                'is_active' => true
            ]
        ];

        foreach ($faqs as $faq) {
            FaqItem::create($faq);
        }
    }
}
