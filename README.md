
<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://www.uit.edu.vn/sites/vi/files/resize/images/Logos/Logo_UIT_Web_Transparent-218x261.png" width="150" alt="UIT Logo"></a></p>

# **THIẾT KẾ HỆ THỐNG THƯƠNG MẠI ĐIỆN TỬ KINH DOANH SẢN PHẨM LEN HANDMADE - LENLAB**

# **Giới thiệu môn học**

- **Tên môn học**: Thiết kế hệ thống thương mại điện tử
- **Mã môn học**: EC312
- **Mã lớp**: EC312.Q11
- **Năm học**: HK1 (2025 - 2026)
- **Giảng viên**: ThS. Trình Trọng Tín

## **Giới thiệu**
**LenLab** là sản phẩm môn học EC312 - Thiết kế hệ thống thương mại điện tử. Đây là hệ thống thương mại điện tử chuyên cung cấp các sản phẩm **len handmade** và đồ thủ công. Dự án hướng tới việc giải quyết các vấn đề của người dùng từ việc tính toán nguyên liệu chính xác cho đến tự động hóa quy trình chăm sóc khách hàng 24/7. Hệ thống hỗ trợ cả sản phẩm vật lý và sản phẩm số, mang lại trải nghiệm mượt mà cho người dùng và hiệu quả cho quản trị viên.

# **Thành viên Nhóm 2**

| STT | Họ tên                  | MSSV     |
|-----|-------------------------|----------|
| 1   | Nguyễn Thành Long       | 23520885 |
| 2   | Nguyễn Thị Quỳnh Như    | 23521128 |
| 3   | Lê Nguyễn Minh Thư      | 23521538 |
| 4   | Võ Ngọc Tuyền           | 23521756 |
| 5   | Đỗ Văn Vũ               | 23521804 |

## **Mục tiêu dự án**
Xây dựng một hệ thống thương mại điện tử chuyên biệt cho các sản phẩm len và đồ thủ công, có khả năng:
- Kinh doanh song song cả sản phẩm vật lý và sản phẩm số.
- Số hóa quy trình bán hàng truyền thống.
- Giải quyết các khó khăn thực tế của người dùng, từ việc tính toán nguyên liệu chính xác đến tự động hóa quy trình chăm sóc khách hàng.

## **Chức năng chính**

### 🛒 **E-commerce Core**
- **Quản lý sản phẩm**:
  - Sản phẩm có nhiều biến thể (kích thước, màu sắc).
- **Giỏ hàng thông minh**:
  - Hỗ trợ cả guest user và người dùng đã đăng nhập.
- **Checkout đa bước**:
  - Địa chỉ → Thanh toán → Xác nhận.
- **Quản lý đơn hàng**:
  - Theo dõi trạng thái đơn hàng: Đang xử lý → Đang giao → Đã giao.
- **Hệ thống voucher**:
  - Mã giảm giá với điều kiện tối thiểu.
- **Tính phí ship**:
  - Tính phí vận chuyển theo 4 zone và thời gian giao hàng.

### 💎 **Sản phẩm số (Digital Products)**
- **Bán file/link số**:
  - Giao hàng tự động qua email.
- **Giới hạn download và thời gian truy cập**:
  - Hệ thống tự động đóng dấu bản quyền (Watermark) thông tin khách hàng lên file hướng dẫn.

### ⭐ **Hệ thống đánh giá**
- **Đánh giá 5 sao với hình ảnh**:
  - Kiểm duyệt admin và phản hồi từ admin.
  - Yêu cầu mua hàng đã xác thực.

### 🎨 **Tùy chỉnh giao diện**
- **Cấu hình logo, favicon, màu sắc**.
- **Quản lý banner trang chủ**.
- **Cài đặt động lưu trong database**.

### 🤖 **AI Chatbot (3 chức năng chính)**

#### 1. **Hỏi đáp 24/7 (FAQ)**
- Trả lời tự động các câu hỏi thường gặp về giao hàng, đổi trả, sản phẩm, thanh toán.

#### 2. **Yêu cầu sản phẩm cá nhân hóa**
- Thu thập thông tin: loại sản phẩm, kích thước, mô tả.
- Upload ảnh tham khảo.
- Workflow hoàn chỉnh: Yêu cầu → Admin trao đổi → Báo giá → Thanh toán → Sản xuất.

#### 3. **Ước tính nguyên liệu**
- Tính toán chi phí nguyên liệu cho các dự án DIY (Do it Yourself).
- Thêm trực tiếp vào giỏ hàng.

## **📊 Quản lý Admin**

### **Dashboard Features**
- Thống kê tổng quan doanh thu, hành vi khách hàng, hiệu suất bán hàng.
- Quản lý đơn hàng với các thao tác batch (sửa đổi nhiều đơn hàng cùng lúc).
- Kiểm duyệt và quản lý đánh giá từ khách hàng.
- Quản lý chatbot và các thông tin phân tích (analytics).
- Cấu hình giao diện động.
- Quản lý voucher và FAQ.

### **Chatbot Management**
- Xem lịch sử chat với khách hàng.
- Quản lý yêu cầu sản phẩm cá nhân hóa.
- Chat hỗ trợ trực tiếp với khách hàng.
- Phân tích và thống kê hành vi sử dụng chatbot.

## **🏗️ Kiến trúc hệ thống**

### **Backend (Laravel 12)**
- **PHP**: >= 8.2
- **Framework**: Laravel 12
- **Database**: MySQL
- **Authentication**: Laravel Breeze + Google OAuth
- **Queue**: Database driver
- **Cache**: Database driver

### **Frontend**
- **Template Engine**: Blade
- **CSS Framework**: Tailwind CSS 3
- **JavaScript**: Alpine.js 3
- **Build Tool**: Vite 7
- **HTTP Client**: Axios

## **📁 Cấu trúc thư mục**

lenlab-project/
├── app/
│   ├── Http/Controllers/     # 40+ Controllers
│   │   ├── Admin/           # Admin controllers

│   │   ├── Auth/            # Authentication controllers
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
│   ├── seeders/            # Data seeders
│   └── data/               # Province/ward data
├── routes/
│   ├── web.php             # Main routes
│   ├── api.php             # API routes
│   └── auth.php            # Auth routes
└── public/
    ├── storage/            # File uploads
    └── product-img/        # Product images

