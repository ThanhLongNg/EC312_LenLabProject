# 🧶 LENLAB - Nền tảng E-commerce với AI Chatbot

Một nền tảng thương mại điện tử hoàn chỉnh được xây dựng trên Laravel 12, chuyên về sản phẩm len thủ công và sản phẩm cá nhân hóa, tích hợp AI Chatbot thông minh.

## 📋 Tổng quan dự án

LENLAB là một hệ thống e-commerce toàn diện với các tính năng:

- **🛍️ Marketplace sản phẩm vật lý** với hệ thống biến thể
- **💻 Nền tảng bán sản phẩm số** (digital products)
- **🤖 AI Chatbot** hỗ trợ khách hàng 24/7
- **🎨 Hệ thống yêu cầu sản phẩm cá nhân hóa**
- **📊 Admin dashboard** quản lý toàn diện
- **📝 Hệ thống blog/content**
- **⭐ Hệ thống đánh giá và review**

## 🚀 Tính năng chính

### 🛒 E-commerce Core
- **Quản lý sản phẩm**: Sản phẩm với nhiều biến thể (kích thước, màu sắc)
- **Giỏ hàng thông minh**: Hỗ trợ cả guest user và user đã đăng nhập
- **Checkout đa bước**: Địa chỉ → Thanh toán → Xác nhận
- **Quản lý đơn hàng**: Theo dõi trạng thái (Đang xử lý → Đang giao → Đã giao)
- **Hệ thống voucher**: Mã giảm giá với điều kiện tối thiểu
- **Tính phí ship**: Theo vùng miền (4 zone) với thời gian giao hàng

### 💎 Sản phẩm số (Digital Products)
- Bán file/link số
- Giao hàng tự động qua email
- Lịch sử mua hàng
- Giới hạn download và thời gian truy cập

### 🤖 AI Chatbot (3 chức năng chính)

#### 1. Hỏi đáp 24/7 (FAQ)
- Trả lời tự động các câu hỏi thường gặp
- Hỗ trợ: giao hàng, đổi trả, sản phẩm, thanh toán

#### 2. Yêu cầu sản phẩm cá nhân hóa
- Thu thập thông tin: loại sản phẩm, kích thước, mô tả
- Upload ảnh tham khảo
- Workflow hoàn chỉnh: Yêu cầu → Admin trao đổi → Báo giá → Thanh toán → Sản xuất

#### 3. Ước tính nguyên liệu
- Tính toán chi phí nguyên liệu cho dự án DIY
- Thêm trực tiếp vào giỏ hàng

### ⭐ Hệ thống đánh giá
- Đánh giá 5 sao với hình ảnh
- Kiểm duyệt admin
- Phản hồi từ admin
- Yêu cầu mua hàng đã xác thực

### 🎨 Tùy chỉnh giao diện
- Cấu hình logo, favicon, màu sắc
- Quản lý banner trang chủ
- Cài đặt động lưu trong database

## 🏗️ Kiến trúc hệ thống

### Backend (Laravel 12)
- **PHP**: 8.2+
- **Framework**: Laravel 12
- **Database**: SQLite (mặc định) hoặc MySQL
- **Authentication**: Laravel Breeze + Google OAuth
- **Queue**: Database driver
- **Cache**: Database driver

### Frontend
- **Template Engine**: Blade
- **CSS Framework**: Tailwind CSS 3
- **JavaScript**: Alpine.js 3
- **Build Tool**: Vite 7
- **HTTP Client**: Axios

### Cấu trúc Database (25+ Models)

#### Models chính:
- **User, Admin**: Quản lý tài khoản
- **Product, ProductVariant**: Sản phẩm và biến thể
- **Order, OrderItem**: Đơn hàng và chi tiết
- **Cart**: Giỏ hàng
- **DigitalProduct, DigitalProductPurchase**: Sản phẩm số
- **CustomProductRequest**: Yêu cầu cá nhân hóa
- **ChatLog, ChatSupportLog**: Lịch sử chat
- **Comment, CommentReply**: Đánh giá và phản hồi
- **Address, Province, Ward**: Địa chỉ và vùng miền
- **Voucher**: Mã giảm giá
- **Post**: Blog
- **Banner**: Banner trang chủ
- **Setting**: Cấu hình hệ thống

## 🛠️ Cài đặt và triển khai

### Yêu cầu hệ thống
- PHP 8.2+
- Composer
- Node.js & npm
- SQLite hoặc MySQL

### Cài đặt nhanh
```bash
# Clone repository
git clone <repository-url>
cd lenlab-project

# Cài đặt dependencies
composer install
npm install

# Cấu hình môi trường
cp .env.example .env
php artisan key:generate

# Chạy migration
php artisan migrate

# Build assets
npm run build

# Khởi động server
php artisan serve
```

### Cài đặt development
```bash
# Sử dụng script setup tự động
composer run setup

# Hoặc chạy development với tất cả services
composer run dev
```

Script `dev` sẽ khởi động:
- Laravel server (port 8000)
- Queue worker
- Log viewer (Pail)
- Vite dev server

## 📁 Cấu trúc thư mục

```
lenlab-project/
├── app/
│   ├── Http/Controllers/     # 40+ Controllers
│   │   ├── Admin/           # Admin controllers
│   │   ├── Auth/            # Authentication
│   │   └── ...              # Public controllers
│   ├── Models/              # 25+ Models
│   ├── Helpers/             # Helper classes
│   │   ├── ShippingHelper.php
│   │   ├── SettingsHelper.php
│   │   └── OrderItemHelper.php
│   ├── Mail/                # Email templates
│   └── View/                # View components
├── resources/
│   ├── views/               # 40+ Blade templates
│   │   ├── admin/          # Admin views
│   │   ├── components/     # Reusable components
│   │   └── ...             # Public views
│   ├── css/                # Tailwind CSS
│   └── js/                 # Alpine.js, Bootstrap
├── database/
│   ├── migrations/         # 50+ Migrations
│   ├── seeders/           # Data seeders
│   └── data/              # Province/ward data
├── routes/
│   ├── web.php            # Main routes
│   ├── api.php            # API routes
│   └── auth.php           # Auth routes
└── public/
    ├── storage/           # File uploads
    └── product-img/       # Product images
```

## 🌐 Routes chính

### Public Routes
- `/` - Trang chủ
- `/san-pham` - Danh sách sản phẩm
- `/san-pham/{id}` - Chi tiết sản phẩm
- `/san-pham-so` - Sản phẩm số
- `/blog` - Blog
- `/cart` - Giỏ hàng
- `/checkout` - Thanh toán

### User Routes (Authenticated)
- `/profile` - Hồ sơ cá nhân
- `/addresses` - Sổ địa chỉ
- `/orders` - Lịch sử đơn hàng
- `/digital-orders` - Đơn hàng số
- `/my-requests` - Yêu cầu cá nhân hóa

### Admin Routes
- `/admin/dashboard` - Tổng quan
- `/admin/products` - Quản lý sản phẩm
- `/admin/orders` - Quản lý đơn hàng
- `/admin/customers` - Quản lý khách hàng
- `/admin/chatbot` - Quản lý chatbot
- `/admin/reviews` - Kiểm duyệt đánh giá

### API Routes
- `/api/products` - API sản phẩm
- `/api/cart/*` - API giỏ hàng
- `/api/chatbot/*` - API chatbot
- `/api/checkout/*` - API thanh toán

## 🔧 Tính năng kỹ thuật

### Shipping Helper
- **4 vùng giao hàng**:
  - ZONE_1 (TP.HCM): 20,000đ
  - ZONE_2 (Miền Nam): 27,000đ  
  - ZONE_3 (Miền Trung): 32,000đ
  - ZONE_4 (Miền Bắc): 37,000đ

### Chatbot Workflow
- **Intent Classification**: Tự động phân loại ý định
- **Context Management**: Nhớ trạng thái cuộc trò chuyện
- **Multi-step Conversations**: Hỗ trợ hội thoại nhiều bước
- **Image Upload**: Upload ảnh trong chat
- **Session Support**: Hỗ trợ cả guest và user đã đăng nhập

### Custom Product Request Flow
1. **Khởi tạo**: User nhấn nút trong chatbot
2. **Thu thập**: Chatbot hỏi thông tin từng bước
3. **Upload ảnh**: Cho phép gửi ảnh tham khảo
4. **Admin trao đổi**: Chat trực tiếp với khách hàng
5. **Báo giá**: Admin chốt giá và thời gian
6. **Thanh toán**: Khách hàng thanh toán 1 lần
7. **Xác nhận**: Admin xác nhận và bắt đầu sản xuất

## 🧪 Testing

### Chạy tests
```bash
# Chạy tất cả tests
composer run test

# Hoặc
php artisan test
```

### Test files có sẵn
- `test_chatbot.html` - Test chatbot độc lập
- `test_*.php` - Các script test riêng lẻ

## 📊 Quản lý Admin

### Dashboard Features
- Thống kê tổng quan
- Quản lý đơn hàng với bulk operations
- Kiểm duyệt đánh giá
- Quản lý chatbot và analytics
- Cấu hình UI động
- Quản lý voucher và FAQ

### Chatbot Management
- Xem lịch sử chat
- Quản lý yêu cầu cá nhân hóa
- Chat support trực tiếp với khách
- Analytics và thống kê

## 🔐 Bảo mật

- **Authentication**: Laravel Breeze + Google OAuth
- **Authorization**: Role-based access control
- **CSRF Protection**: Tích hợp sẵn
- **Input Validation**: Form requests
- **File Upload**: Validation và sanitization
- **SQL Injection**: Eloquent ORM protection

## 🚀 Performance

- **Caching**: Database cache cho settings
- **Queue Jobs**: Background processing
- **Asset Optimization**: Vite bundling
- **Database Optimization**: Proper indexing
- **Image Optimization**: Smart path resolution

## 📈 Mở rộng tương lai

### AI Integration
- Tích hợp OpenAI API thật
- Voice chat support
- Multilingual support

### Advanced Features
- Real-time notifications
- Advanced analytics
- Webhook integrations
- Mobile app API

### Performance Optimization
- Redis caching
- CDN integration
- Database optimization
- Load balancing

## 🤝 Đóng góp

1. Fork repository
2. Tạo feature branch (`git checkout -b feature/AmazingFeature`)
3. Commit changes (`git commit -m 'Add some AmazingFeature'`)
4. Push to branch (`git push origin feature/AmazingFeature`)
5. Tạo Pull Request

## 📄 License

Dự án này được phân phối dưới giấy phép [MIT License](https://opensource.org/licenses/MIT).

## 📞 Liên hệ

- **Email**: support@lenlab.com
- **Website**: https://lenlab.com
- **Documentation**: Xem thêm trong thư mục `docs/`

---

**LENLAB** - Nền tảng e-commerce thông minh với AI Chatbot, sẵn sàng cho production và dễ dàng mở rộng!
