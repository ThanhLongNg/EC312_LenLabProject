<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\ChatLog;
use App\Models\CustomProductRequest;
use App\Models\MaterialEstimate;
use Carbon\Carbon;

class ChatbotDataSeeder extends Seeder
{
    public function run()
    {
        $this->command->info('Creating sample chatbot data...');

        // Get some existing users or create new ones
        $users = User::limit(10)->get();
        if ($users->count() < 5) {
            // Create some sample users if not enough exist
            for ($i = 1; $i <= 5; $i++) {
                User::create([
                    'name' => "Khách hàng $i",
                    'email' => "customer$i@example.com",
                    'password' => bcrypt('password'),
                    'email_verified_at' => now(),
                ]);
            }
            $users = User::limit(10)->get();
        }

        // Sample conversations data
        $conversations = [
            [
                'customer_name' => 'Nguyễn Thùy Linh',
                'messages' => [
                    ['user' => 'Xin chào, tôi muốn hỏi về sản phẩm len', 'bot' => 'Xin chào! Tôi có thể giúp bạn tìm hiểu về các sản phẩm len của chúng tôi. Bạn quan tâm đến loại sản phẩm nào?'],
                    ['user' => 'Tôi muốn mua khăn len cho mùa đông', 'bot' => 'Chúng tôi có nhiều mẫu khăn len đẹp cho mùa đông. Bạn thích màu gì và kích thước như thế nào?'],
                    ['user' => 'Màu be hoặc nâu, kích thước vừa phải', 'bot' => 'Tuyệt vời! Tôi sẽ gửi bạn một số gợi ý sản phẩm phù hợp.']
                ]
            ],
            [
                'customer_name' => 'Trần Văn Nam',
                'messages' => [
                    ['user' => 'Làm riêng sản phẩm được không?', 'bot' => 'Tất nhiên! Chúng tôi nhận đặt làm sản phẩm theo yêu cầu. Bạn muốn làm sản phẩm gì?'],
                    ['user' => 'Tôi muốn làm thú bông cho con', 'bot' => 'Thú bông là một lựa chọn tuyệt vời! Bạn muốn làm con vật gì và kích thước bao nhiêu?']
                ]
            ],
            [
                'customer_name' => 'Phạm Minh Tuấn',
                'messages' => [
                    ['user' => 'Giao hàng mất bao lâu?', 'bot' => 'Chúng tôi giao hàng toàn quốc trong 2-5 ngày làm việc. Phí ship từ 25.000đ tùy theo khu vực. Miễn phí ship cho đơn hàng từ 500.000đ.'],
                    ['user' => 'Tôi ở Hà Nội thì sao?', 'bot' => 'Hà Nội thuộc khu vực ưu tiên, thời gian giao hàng 1-2 ngày làm việc. Phí ship 25.000đ hoặc miễn phí với đơn từ 500k.']
                ]
            ],
            [
                'customer_name' => 'Lê Thị Mai',
                'messages' => [
                    ['user' => 'Ước tính len cần thiết để đan áo', 'bot' => 'Tôi sẽ giúp bạn ước tính số lượng len cần thiết! Bạn muốn đan áo size gì?'],
                    ['user' => 'Size M cho nữ', 'bot' => 'Áo len nữ size M thường cần khoảng 350-400g len. Bạn muốn dùng loại len gì?'],
                    ['user' => 'Len cotton', 'bot' => 'Với len cotton, bạn sẽ cần khoảng 350g. Chi phí ước tính khoảng 160.000đ cho nguyên liệu.']
                ]
            ],
            [
                'customer_name' => 'Hoàng Minh Đức',
                'messages' => [
                    ['user' => 'Có hướng dẫn đan không?', 'bot' => 'Có! Chúng tôi có sản phẩm số bao gồm video hướng dẫn chi tiết và file PDF pattern.'],
                    ['user' => 'Giá bao nhiêu?', 'bot' => 'Giá sản phẩm số từ 50.000đ - 200.000đ tùy độ phức tạp. Bạn quan tâm đến sản phẩm nào?']
                ]
            ]
        ];

        // Create conversations with realistic timing
        foreach ($conversations as $index => $conversation) {
            $user = $users->random();
            $sessionId = 'session_' . time() . '_' . $index . '_' . uniqid();
            
            // Create conversation over the last few days
            $baseTime = Carbon::now()->subDays(rand(0, 7))->subHours(rand(0, 23));
            
            foreach ($conversation['messages'] as $msgIndex => $message) {
                $messageTime = $baseTime->copy()->addMinutes($msgIndex * rand(2, 10));
                
                // User message
                ChatLog::create([
                    'session_id' => $sessionId,
                    'user_id' => $user->id,
                    'user_message' => $message['user'],
                    'bot_reply' => $message['bot'],
                    'intent' => $this->detectIntent($message['user']),
                    'context' => null,
                    'created_at' => $messageTime,
                    'updated_at' => $messageTime,
                ]);
            }
        }

        // Create some custom product requests
        $customRequests = [
            [
                'product_type' => 'Thú bông',
                'size' => 'Nhỏ (15cm)',
                'description' => 'Thú bông hình gấu màu nâu, mắt to tròn, có thể ôm được',
                'status' => 'pending_admin_response'
            ],
            [
                'product_type' => 'Áo len',
                'size' => 'Size M',
                'description' => 'Áo len cổ tròn màu xanh navy, kiểu dáng basic, phù hợp mùa đông',
                'status' => 'admin_responded',
                'admin_response' => 'Chúng tôi đã xem yêu cầu của bạn. Áo len này có thể làm được với giá khoảng 450.000đ, thời gian 7-10 ngày.',
                'estimated_price' => 450000
            ],
            [
                'product_type' => 'Túi xách',
                'size' => 'Vừa (30x25cm)',
                'description' => 'Túi xách len màu be, có dây đeo, phong cách vintage',
                'status' => 'confirmed',
                'estimated_price' => 320000,
                'deposit_percentage' => 30
            ]
        ];

        foreach ($customRequests as $index => $requestData) {
            $user = $users->random();
            $sessionId = 'custom_' . time() . '_' . $index . '_' . uniqid();
            
            $request = CustomProductRequest::create([
                'session_id' => $sessionId,
                'user_id' => $user->id,
                'product_type' => $requestData['product_type'],
                'size' => $requestData['size'],
                'description' => $requestData['description'],
                'status' => $requestData['status'],
                'admin_response' => $requestData['admin_response'] ?? null,
                'estimated_price' => $requestData['estimated_price'] ?? null,
                'deposit_percentage' => $requestData['deposit_percentage'] ?? 30,
                'created_at' => Carbon::now()->subDays(rand(1, 5)),
            ]);

            if (isset($requestData['estimated_price'])) {
                $request->update([
                    'deposit_amount' => $request->calculateDepositAmount(),
                    'remaining_amount' => $request->calculateRemainingAmount()
                ]);
            }
        }

        // Create some material estimates
        $estimates = [
            [
                'product_type' => 'áo len',
                'size' => 'M',
                'yarn_type' => 'cotton',
                'materials' => [
                    ['name' => 'Len cotton', 'quantity' => 350, 'unit' => 'gram', 'cost' => 158000],
                    ['name' => 'Kim đan', 'quantity' => 1, 'unit' => 'bộ', 'cost' => 25000]
                ],
                'total_cost' => 183000
            ],
            [
                'product_type' => 'khăn',
                'size' => 'L',
                'yarn_type' => 'wool',
                'materials' => [
                    ['name' => 'Len wool', 'quantity' => 250, 'unit' => 'gram', 'cost' => 163000]
                ],
                'total_cost' => 163000
            ]
        ];

        foreach ($estimates as $index => $estimateData) {
            $user = $users->random();
            $sessionId = 'estimate_' . time() . '_' . $index . '_' . uniqid();
            
            MaterialEstimate::create([
                'session_id' => $sessionId,
                'user_id' => $user->id,
                'product_type' => $estimateData['product_type'],
                'size' => $estimateData['size'],
                'yarn_type' => $estimateData['yarn_type'],
                'estimated_materials' => $estimateData['materials'],
                'total_estimated_cost' => $estimateData['total_cost'],
                'created_at' => Carbon::now()->subDays(rand(0, 3)),
            ]);
        }

        $this->command->info('Sample chatbot data created successfully!');
        $this->command->info('- Added ' . count($conversations) . ' conversation threads');
        $this->command->info('- Added ' . count($customRequests) . ' custom product requests');
        $this->command->info('- Added ' . count($estimates) . ' material estimates');
    }

    private function detectIntent($message)
    {
        $message = strtolower($message);
        
        if (strpos($message, 'làm riêng') !== false || strpos($message, 'đặt làm') !== false) {
            return 'CUSTOM_REQUEST';
        }
        
        if (strpos($message, 'ước tính') !== false || strpos($message, 'cần bao nhiêu') !== false) {
            return 'MATERIAL_ESTIMATE';
        }
        
        return 'FAQ';
    }
}